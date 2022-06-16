<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 */

namespace App\Utilities;

use App\Fresns\Client\ClientAble;
use Psr\Http\Message\ResponseInterface;

class ApiUtility implements \ArrayAccess
{
    use ClientAble;

    protected array $result = [];

    public function getBaseUri(): ?string
    {
        $isLocal = true;
        $localApiHost = config('app.url');
        $remoteApiHost = 'https://api.fresns.cn';

        $apiHost = $isLocal ? $localApiHost : $remoteApiHost;

        return $apiHost;
    }

    public function handleEmptyResponse(?string $content = null, ?ResponseInterface $response = null)
    {
        info('empty response, ApiException: '.var_export($content, true));
        throw new \Exception(sprintf('ApiException: %s', $response?->getReasonPhrase()), $response?->getStatusCode());
    }

    public function isErrorResponse(array $data): bool
    {
        return $data['code'] !== 0;
    }

    public function handleErrorResponse(?string $content = null, array $data = [])
    {
        info('error response, ApiException: '.var_export($content, true));
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
