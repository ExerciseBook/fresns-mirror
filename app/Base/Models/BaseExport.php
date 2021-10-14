<?php

/*
 * Fresns (https://fresns.cn)
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

    // initialization
    public function __construct($invoices = [])
    {
        $this->invoices = $invoices;
    }

    // Export
    public function collection()
    {
        return $this->invoices;
    }

    // table header
    public function headings(): array
    {
        return ['ID', 'Name', 'Creation time'];
    }

    // Exported fields
    public function map($item): array
    {
        return [
            $item->id,
            $item->name,
            $item->created_at,
        ];
    }
}
