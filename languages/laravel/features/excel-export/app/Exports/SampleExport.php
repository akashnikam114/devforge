<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SampleExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Project',
            'Generated At',
        ];
    }

    public function collection(): Collection
    {
        return collect([
            [
                'project' => '__PROJECT_NAME__',
                'generated_at' => now()->toDateTimeString(),
            ],
        ]);
    }
}
