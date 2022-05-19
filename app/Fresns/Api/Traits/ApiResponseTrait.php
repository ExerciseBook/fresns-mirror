<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 */

namespace App\Fresns\Api\Traits;

use App\Helpers\AppHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    public static function string2utf8($string = '')
    {
        if (empty($string)) {
            return $string;
        }

        $encoding_list = [
            'ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5',
        ];

        $encode = mb_detect_encoding($string, $encoding_list);

        $string = mb_convert_encoding($string, 'UTF-8', $encode);

        return $string;
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

        $message = static::string2utf8($message);

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
            }, $paginate->items()),
        ]);
    }
}
