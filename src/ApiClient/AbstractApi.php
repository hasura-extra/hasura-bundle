<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\ApiClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractApi
{
    public const VERSION = 'v1';

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function request(array $payload): ResponseInterface
    {
        $url = sprintf('%s/%s', self::VERSION, $this->apiPath());

        return $this->httpClient->request(
            'POST',
            $url,
            [
                'json' => $payload,
            ]
        );
    }

    abstract protected function apiPath(): string;
}
