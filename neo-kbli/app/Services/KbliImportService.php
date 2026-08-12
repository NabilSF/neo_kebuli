<?php

namespace App\Services;

use App\Models\KbliItem;
use App\Helpers\CacheKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class KbliImportService
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function generateTemplate(string $format)
    {
        // Simple array export for template
        $export = new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithMultipleSheets {
            public function array(): array
            {
                return [
                    ['1', '01111', 'Pertanian Jagung', 'Deskripsi Jagung', '1']
                ];
            }
            public function headings(): array
            {
                return ['section_id', 'code', 'name', 'description', 'is_active'];
            }
            public function title(): string
            {
                return 'Data';
            }
            public function sheets(): array
            {
                return [
                    $this,
                    new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithTitle {
                        public function array(): array
                        {
                            return [
                                ['Petunjuk Pengisian'],
                                ['section_id', 'ID dari Section KBLI (harus valid)'],
                                ['code', 'Kode KBLI (Unik)'],
                                ['name', 'Nama KBLI'],
                                ['description', 'Deskripsi opsional'],
                                ['is_active', '1 untuk aktif, 0 untuk non-aktif']
                            ];
                        }
                        public function title(): string
                        {
                            return 'Petunjuk';
                        }
                    }
                ];
            }
        };

        if ($format === 'csv') {
            return Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function array(): array {
                    return [['1', '01111', 'Pertanian Jagung', 'Deskripsi', '1']];
                }
                public function headings(): array {
                    return ['section_id', 'code', 'name', 'description', 'is_active'];
                }
            }, 'kbli_template.csv');
        }

        return Excel::download($export, 'kbli_template.xlsx');
    }

    public function parseAndPreview(UploadedFile $file): string
    {
        $cacheKey = 'import_preview_' . Str::random(16);
        
        $data = Excel::toArray(new class {}, $file)[0];
        $headers = array_shift($data);
        
        $preview = [
            'total_rows' => count($data),
            'valid_rows' => 0,
            'invalid_rows' => 0,
            'rows' => []
        ];

        $existingCodes = KbliItem::pluck('code')->toArray();
        $existingSections = \App\Models\KbliSection::where('is_active', true)->pluck('id')->toArray();
        $seenCodes = [];

        foreach ($data as $index => $row) {
            $rowAssoc = array_combine($headers, array_pad($row, count($headers), null));
            $code = $rowAssoc['code'] ?? null;
            $status = 'VALID';
            $reason = '';

            if (empty($code) || empty($rowAssoc['name']) || empty($rowAssoc['section_id'])) {
                $status = 'ERROR';
                $reason = 'Missing required fields (code, name, section_id). ';
            } elseif (!preg_match('/^\d{5}$/', $code)) {
                $status = 'ERROR';
                $reason = 'Kode harus tepat 5 digit angka. ';
            } elseif (!in_array((int)$rowAssoc['section_id'], $existingSections)) {
                $status = 'ERROR';
                $reason = 'Invalid section_id. ';
            } elseif (in_array($code, $existingCodes)) {
                $status = 'ERROR';
                $reason = 'Code already exists in database. ';
            } elseif (in_array($code, $seenCodes)) {
                $status = 'ERROR';
                $reason = 'Duplicate code in this file. ';
            }

            if ($status === 'VALID') {
                $seenCodes[] = $code;
                $preview['valid_rows']++;
            } else {
                $preview['invalid_rows']++;
            }

            $preview['rows'][] = [
                'row_num' => $index + 2,
                'code' => $code,
                'name' => $rowAssoc['name'] ?? '',
                'section_id' => $rowAssoc['section_id'] ?? '',
                'description' => $rowAssoc['description'] ?? '',
                'is_active' => $rowAssoc['is_active'] ?? 1,
                'status' => $status,
                'reason' => $reason
            ];
        }

        Cache::put($cacheKey, $preview, now()->addMinutes(30));
        return $cacheKey;
    }

    public function confirmImport(string $cacheKey)
    {
        $preview = Cache::get($cacheKey);
        if (!$preview) {
            throw new \Exception('Preview data expired or invalid.');
        }

        $validRows = array_filter($preview['rows'], fn($r) => $r['status'] === 'VALID');
        if (count($validRows) === 0) {
            throw new \Exception('No valid rows to import.');
        }

        DB::transaction(function () use ($validRows) {
            foreach ($validRows as $row) {
                $item = KbliItem::create([
                    'section_id' => $row['section_id'],
                    'code' => $row['code'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'is_active' => (bool)$row['is_active'],
                ]);

                $this->activityLogService->logActivity(
                    entity: $item,
                    action: 'kbli.imported',
                    notes: 'Admin imported KBLI item directly',
                    properties: ['code' => $item->code]
                );
            }

            CacheKey::invalidateKbliCategories();
        });

        Cache::forget($cacheKey);
    }
}
