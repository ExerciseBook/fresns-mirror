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

    // 初始化
    public function __construct($invoices = [])
    {
        $this->invoices = $invoices;
    }

    // 导出
    public function collection()
    {
        return $this->invoices;
    }

    // 表头
    public function headings(): array
    {
        return ['ID', 'Name', '创建时间'];
    }

    // 导出的字段
    public function map($item): array
    {
        return [
            $item->id,
            $item->name,
            $item->created_at,
        ];
    }
}
