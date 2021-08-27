<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Models;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BaseExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;

    protected $invoices;

    // Initial
    public function __construct($invoices = [])
    {
        $this->invoices = $invoices;
    }

    // Export
    public function collection()
    {
        return $this->invoices;
    }

    // Table Name
    public function headings(): array
    {
        return ['ID', 'Name', 'Created Time'];
    }

    // Exported Column
    public function map($item): array
    {
        return [
            $item->id,
            $item->name,
            $item->created_at,
        ];
    }
}
