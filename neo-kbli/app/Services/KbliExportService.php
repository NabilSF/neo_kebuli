<?php

namespace App\Services;

use App\Models\KbliItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KbliExportService implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return KbliItem::query()->with('section.category');
    }

    public function headings(): array
    {
        return [
            'section_id',
            'code',
            'name',
            'description',
            'is_active'
        ];
    }

    public function map($kbli): array
    {
        return [
            $kbli->section_id,
            $kbli->code,
            $kbli->name,
            $kbli->description,
            $kbli->is_active ? 1 : 0,
        ];
    }
}
