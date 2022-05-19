<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Fresns\Api\Traits\ApiResponseTrait;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ApiResponseTrait;

    public function getPaginate($paginate, ?callable $callable = null)
    {
        $meta = [
            'total' => $paginate->total(),
            'current' => $paginate->currentPage(),
            'pageSize' => $paginate->perPage(),
            'lastPage' => $paginate->lastPage(),
        ];

        $data = $paginate->items();

        if ($callable) {
            $data = collect($paginate->items())->map($callable);
        }

        return [
            'paginate' => $meta,
            'list' => $data,
        ];
    }
}
