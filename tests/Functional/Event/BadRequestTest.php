<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Event\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BadRequestContentTest extends WebTestCase
{
    /**
     * @dataProvider requestInformation
     */
    public function testBadRequest(array $information, int $responseStatusCode): void
    {
        [$method, $contentType, $content] = $information;
        $client = $this->createClient();
        $client->request(
            $method,
            '/hasura_event',
            server: ['CONTENT_TYPE' => $contentType, 'HTTP_ACCEPT' => $contentType],
            content: $content
        );

        $this->assertResponseStatusCodeSame($responseStatusCode);
    }

    public function requestInformation(): array
    {
        return [
            [
                [
                    'GET',
                    'application/json',
                    '',
                ],
                Response::HTTP_METHOD_NOT_ALLOWED,
            ],
            [
                [
                    'POST',
                    'application/test',
                    '',
                ],
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
            ],
            [
                [
                    'POST',
                    'application/json',
                    '',
                ],
                Response::HTTP_BAD_REQUEST,
            ],
            [
                [
                    'POST',
                    'application/json',
                    json_encode(
                        [
                            'id' => 'a',
                            'table' => ['schema' => 'public', 'name' => 'abc'],
                            'event' => [
                                'old' => null,
                                'new' => null,
                            ],
                            'delivery_info' => [],
                            'created_at' => '1',
                        ]
                    ),
                ],
                Response::HTTP_BAD_REQUEST,
            ],
        ];
    }
}
