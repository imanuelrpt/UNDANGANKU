<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class GuestsImport implements ToCollection
{
    /**
     * @var array<int, array<int, mixed>>
     */
    private array $rows = [];

    public function collection(Collection $rows): void
    {
        $this->rows = $rows->map(fn ($row): array => $row->all())->all();
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function rows(): array
    {
        return $this->rows;
    }
}
