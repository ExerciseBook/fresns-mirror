<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 */

namespace App\Fresns\Web;

use App\Fresns\Client\Clientable;

/**
 * 并发请求示例
 * 
$client = \App\Fresns\Web\Client::make();
$results = $client->unwrap([
    'configs' => $client->getAsync('/api/v2/global/configs'),
    'roles'   => $client->getAsync('/api/v2/global/roles'),
]);

dd($results, $results['configs'], $results['roles']);
 */
class Client implements \ArrayAccess
{
    use Clientable;

    /** @var \GuzzleHttp\Psr7\Response */
    protected $response;

    protected array $result = [];
    
    public function getBaseUri(): ?string
    {
        $apiHost = app()->environment('local') ? config('app.url') : 'https://api.fresns.cn';

        return $apiHost;
    }

    public function handleEmptyResponse(?string $content = null, ?\Psr\Http\Message\ResponseInterface $response = null)
    {
        info('empty reponse, ApiException: '.var_export($content, true));
        throw new \Exception(sprintf('ApiException: %s', $response?->getReasonPhrase()), $response?->getStatusCode());
    }

    public function isErrorResponse(array $data): bool
    {
        return $data['code'] !== 0;
    }

    public function handleErrorResponse(?string $content = null, array $data = [])
    {
        info('error reponse, ApiException: '.var_export($content, true));
        throw new \Exception(sprintf('ApiException: %s', $data['msg'] ?? $data['exception']), $data['code']);
    }

    public function hasPaginate(): bool
    {
        return !! $this['data.paginate'];
    }

    public function getTotal(): ?int
    {
        return $this['data.paginate.total'];
    }

    public function getPageSize(): ?int
    {
        return $this['data.paginate.pageSize'];
    }

    public function getCurrentPage(): ?int
    {
        return $this['data.paginate.currentPage'];
    }

    public function getLastPage(): ?int
    {
        return $this['data.paginate.lastPage'];
    }

    public function getDataList(): static|array|null
    {
        return $this['data.list']->toArray();
    }
}
