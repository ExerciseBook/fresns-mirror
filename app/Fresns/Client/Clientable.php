<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Fresns\Web\Exceptions\ErrorException;

trait Clientable
{
    /** @var Response */
    protected $response;

    protected array $data = [];

    public static function make(): static|Utils|Client
    {
        return new static();
    }

    abstract public function getBaseUri(): ?string;

    public function getOptions()
    {
        return [
            'base_uri' => $this->getBaseUri(),
            'timeout' => 5, // Request 5s timeout
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];
    }

    public function getHttpClient()
    {
        return new Client($this->getOptions());
    }

    public function castResponse($response)
    {
        $data = json_decode($content = $response->getBody()->getContents(), true) ?? [];

        if (empty($data)) {
            info('empty response, ApiException: ' . var_export($content, true));
            throw new ErrorException($response?->getReasonPhrase(), $response?->getStatusCode());
        }

        if (isset($data['code']) && $data['code'] != 0) {
            info('error response, ApiException: ' . var_export($content, true));

            $message = $data['message'] ?? $data['exception'] ?? '';
            if (empty($message)) {
                $message = 'Unknown api error';
            } else if ($data['data'] ?? null) {
                $message = "{$message} " . head($data['data']) ?? '';
            }

            throw new ErrorException($message, $data['code'] ?? 0);
        }

        return $data;
    }

    public function paginate()
    {
        if (!data_get($this->result, 'data.paginate', false)) {
            return null;
        }

        $paginate = new LengthAwarePaginator(
            items: data_get($this->result, 'data.list'),
            total: data_get($this->result, 'data.paginate.total'),
            perPage: data_get($this->result, 'data.paginate.pageSize'),
            currentPage: data_get($this->result, 'data.paginate.currentPage'),
        );

        $paginate
            ->withPath('/' . \request()->path())
            ->withQueryString();

        return $paginate;
    }

    public function unwrapRequests(array $requests)
    {
        $results = $this->unwrap($requests);

        if (method_exists($this, 'caseUnwrapRequests')) {
            $results = $this->caseUnwrapRequests($results);
        }

        return $results;
    }

    public function __call(string $method, array $args)
    {
        $result = $this->forwardCall($method, $args);

        if (method_exists($this, 'caseForwardCallResult')) {
            $result = $this->caseForwardCallResult($result);
        }

        return $result;
    }

    public function forwardCall($method, $args)
    {
        // Asynchronous requests
        if (method_exists(Utils::class, $method)) {
            $results = call_user_func_array([Utils::class, $method], $args);

            if (!is_array($results)) {
                return $results;
            }

            $data = [];
            foreach ($results as $key => $promise) {
                $data[$key] = $this->castResponse($promise);
            }

            $this->data = $data;

            return $this->data;
        }

        // Synchronization Request
        if (method_exists($this->getHttpClient(), $method)) {
            $this->response = $this->getHttpClient()->$method(...$args);

            return $this->response;
        }

        // Response results processing
        if ($this->response instanceof Response) {
            $this->data = $this->castResponse($this->response);
        }

        return $this->data;
    }
}
