<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Fresns\Api\Traits\ApiResponseTrait;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Pagination\LengthAwarePaginator;

class Controller extends BaseController
{
    use ApiResponseTrait;

    public function fresnsPaginate($items, $total, $pageSize)
    {
        $paginate = new LengthAwarePaginator(
            items: $items,
            total: $total,
            perPage: $pageSize,
            currentPage: \request('page'),
        );

        $paginate
            ->withPath('/'.\request()->path())
            ->withQueryString();

        return $this->paginate($paginate);
    }
}
