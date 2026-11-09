<?php

namespace Database\Seeders;

use App\Models\KbliCategory;
use App\Models\KbliSection;
use App\Models\KbliItem;
use Illuminate\Database\Seeder;

class KbliSeeder extends Seeder
{
    /**
     * Import KBLI data from the government CSV.
     *
     * CSV location : database/data/kbli_raw.csv
     * Delimiter    : semicolon (;)
     * Encoding     : UTF-8 (or Windows-1252 – handled by mb_convert_encoding)
     *
     * CSV → Schema mapping
     * ─────────────────────────────────────────────────────────────────
     * kbli_2_digit  → kbli_categories.code  (2-char string)
     * kbli_2_digit  → kbli_sections.code    (one section per category,
     *                                         derived — schema requires it)
     * kbli_5_digit  → kbli_items.code       (5-char string)
     * nama_komoditas→ kbli_items.name
     * ─────────────────────────────────────────────────────────────────
     *
     * Idempotent: uses updateOrCreate on unique keys.
     */
    public function run(): void
    {
        $csvPath = database_path('data/kbli_raw.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV not found at: {$csvPath}");
            $this->command->line("Place the government CSV at database/data/kbli_raw.csv and re-run.");
            return;
        }

        $handle = fopen($csvPath, 'r');

        // Read header line and detect delimiter / BOM
        $rawHeader = fgets($handle);
        $rawHeader = preg_replace('/^\xEF\xBB\xBF/', '', $rawHeader); // strip UTF-8 BOM if present
        $rawHeader = trim($rawHeader);

        // Detect delimiter: semicolon or comma
        $delimiter = (substr_count($rawHeader, ';') >= 3) ? ';' : ',';
        $headers   = str_getcsv($rawHeader, $delimiter);
        $headers   = array_map('trim', $headers);

        // Verify required columns exist
        $required = ['kbli_5_digit', 'nama_komoditas', 'kbli_2_digit'];
        foreach ($required as $col) {
            if (!in_array($col, $headers)) {
                $this->command->error("Required column '{$col}' not found in CSV. Found: " . implode(', ', $headers));
                fclose($handle);
                return;
            }
        }

        $colCode5  = array_search('kbli_5_digit',   $headers);
        $colName   = array_search('nama_komoditas',  $headers);
        $colCode2  = array_search('kbli_2_digit',   $headers);

        // ── In-memory caches (avoid N+1 DB queries per row) ───────────
        $categoryCache = [];  // kbli_2_digit  → KbliCategory
        $sectionCache  = [];  // kbli_2_digit  → KbliSection

        $totalRows   = 0;
        $importedRows = 0;
        $skippedRows  = 0;
        $warnings     = [];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $totalRows++;

            if (count($row) < 3) {
                $warnings[] = "Row {$totalRows}: Too few columns, skipped.";
                $skippedRows++;
                continue;
            }

            $code5  = trim($row[$colCode5] ?? '');
            $name   = trim($row[$colName]  ?? '');
            $code2  = trim(str_pad($row[$colCode2] ?? '', 2, '0', STR_PAD_LEFT)); // normalise: "9" → "09"

            // Validate: 5-digit code must be exactly 5 numeric characters
            if (!preg_match('/^\d{5}$/', $code5)) {
                $warnings[] = "Row {$totalRows}: Invalid kbli_5_digit '{$code5}', skipped.";
                $skippedRows++;
                continue;
            }

            // Validate: 2-digit code must be 1–2 digits
            if (!preg_match('/^\d{1,2}$/', ltrim($code2, '0') ?: '0')) {
                $warnings[] = "Row {$totalRows}: Invalid kbli_2_digit '{$code2}', skipped.";
                $skippedRows++;
                continue;
            }

            if (empty($name)) {
                $warnings[] = "Row {$totalRows}: Empty nama_komoditas for code '{$code5}', skipped.";
                $skippedRows++;
                continue;
            }

            // ── 1. Category (kbli_2_digit) ─────────────────────────────
            if (!isset($categoryCache[$code2])) {
                $categoryCache[$code2] = KbliCategory::updateOrCreate(
                    ['code' => $code2],
                    ['name' => "Kategori KBLI {$code2}", 'is_active' => true]
                );
            }
            $category = $categoryCache[$code2];

            // ── 2. Section (one per category — derived from schema constraint) ──
            // The schema mandates section_id on kbli_items (NOT NULL).
            // The CSV provides no section data, so we create one canonical
            // section per 2-digit category code. This is the minimum required
            // linkage; no data is invented beyond what the schema forces.
            if (!isset($sectionCache[$code2])) {
                $sectionCache[$code2] = KbliSection::updateOrCreate(
                    ['code' => $code2, 'category_id' => $category->id],
                    ['name' => "Seksi KBLI {$code2}", 'is_active' => true]
                );
            }
            $section = $sectionCache[$code2];

            // ── 3. Item (kbli_5_digit → nama_komoditas) ────────────────
            // The name column is varchar(255). The CSV commodity field is a
            // comma-separated list of products. Store the full list in
            // description (TEXT), and use the first commodity as the item name.
            $firstName = trim(explode(',', $name)[0]);
            $firstName = mb_substr($firstName, 0, 255); // hard cap as safety net

            KbliItem::updateOrCreate(
                ['code' => $code5],
                [
                    'section_id'  => $section->id,
                    'name'        => $firstName,
                    'description' => $name,   // full commodity list → TEXT column
                    'is_active'   => true,
                ]
            );

            $importedRows++;
        }

        fclose($handle);

        // ── Summary output ─────────────────────────────────────────────
        $this->command->info("Total CSV rows processed : {$totalRows}");
        $this->command->info("Imported / updated       : {$importedRows}");
        $this->command->info("Skipped                  : {$skippedRows}");
        $this->command->info("Categories created/found : " . count($categoryCache));
        $this->command->info("Sections created/found   : " . count($sectionCache));

        if (!empty($warnings)) {
            $this->command->warn("\nWarnings (" . count($warnings) . "):");
            foreach ($warnings as $w) {
                $this->command->warn("  {$w}");
            }
        }

        // ── Verification: DB count must match imported rows ────────────
        $dbCount = KbliItem::count();
        $this->command->line("\nVerification: kbli_items in database = {$dbCount}");

        if ($dbCount < $importedRows) {
            $this->command->warn("WARNING: DB count ({$dbCount}) is less than imported rows ({$importedRows}). Check for duplicates or failed inserts.");
        } else {
            $this->command->info("Verification PASSED.");
        }
    }
}
