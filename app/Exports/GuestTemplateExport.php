<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GuestTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection(): Collection
    {
        return collect([
            ['Rina Wulandari', 'Keluarga', '081234567890', 2],
            ['Dimas Pratama', 'Teman', '081298765432', 1],
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['nama', 'grup', 'no wa', 'pax'];
    }
}
