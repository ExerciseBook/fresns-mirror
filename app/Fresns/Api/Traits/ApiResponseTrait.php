<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 */

namespace App\Fresns\Api\Traits;

use App\Helpers\AppHelper;
use App\Helpers\StrHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
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

    public function paginate(LengthAwarePaginator $paginate, ?callable $callable = null)
    {
        return $this->success([
            'paginate' => [
                'total' => $paginate->total(),
                'currentPage' => $paginate->currentPage(),
                'pageSize' => $paginate->perPage(),
                'lastPage' => $paginate->lastPage(),
            ],
            'list' => array_map(function ($item) use ($callable) {
                if ($callable) {
                    return $callable($item) ?? $item;
                }

                return $item;
            },
            $paginate->items()),
        ]);
    }

    public function success($data = null, $message = 'success', $code = 0, $headers = [])
    {
        if (is_string($data)) {
            $code = $message;
            $message = $data;
            $data = [];
        }

        // paginate data
        $meta = [];
        $paginate = [];
        if (isset($data['data']) && isset($data['paginate'])) {
            extract($data);
        }

        $message = StrHelper::stringToUtf8($message);

        $data = $data ?: null;
        $fresnsResponse = compact('code', 'message', 'data') + array_filter(compact('paginate'));

        return \response(
            \json_encode($fresnsResponse, \JSON_UNESCAPED_SLASHES|\JSON_UNESCAPED_UNICODE|\JSON_PRETTY_PRINT),
            Response::HTTP_OK,
            array_merge([
                'Fresns-Version' => AppHelper::VERSION,
                'Fresns-Api' => 'v2',
                'Fresns-Author' => 'Jarvis Tang',
                'Content-Type' => 'application/json',
            ], $headers)
        );
    }

    public function failure($code = 3e4, $message = 'unknown error', $data = null, $headers = [])
    {
        if (! \request()->wantsJson()) {
            $message = \json_encode(compact('code', 'message', 'data'), \JSON_UNESCAPED_SLASHES|\JSON_UNESCAPED_UNICODE|\JSON_PRETTY_PRINT);
            if (!array_key_exists($code, Response::$statusTexts)) {
                $code = 200;
            }

            return \response(
                $message,
                $code,
                array_merge([
                    'Fresns-Version' => AppHelper::VERSION,
                    'Fresns-Api' => 'v2',
                    'Fresns-Author' => 'Jarvis Tang',
                    'Content-Type' => 'application/json',
                ], $headers)
            );
        }

        return $this->success($data, $message ?: 'unknown error', $code ?: 3e4, $headers);
    }
}
