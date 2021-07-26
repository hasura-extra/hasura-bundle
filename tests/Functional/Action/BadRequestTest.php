<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\Action\Functional;

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
            '/hasura_action',
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
                    json_encode(['input' => [], 'session_variables' => [], 'action' => ['name' => 'test']])
                ],
                Response::HTTP_METHOD_NOT_ALLOWED
            ],
            [
                [
                    'POST',
                    'application/test',
                    json_encode(['input' => [], 'session_variables' => [], 'action' => ['name' => 'test']])
                ],
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE
            ],
            [
                [
                    'POST',
                    'application/json',
                    json_encode(['input' => null, 'session_variables' => [], 'action' => ['name' => 'test']])
                ],
                Response::HTTP_BAD_REQUEST
            ],
            [
                [
                    'POST',
                    'application/json',
                    json_encode(['input' => [], 'session_variables' => null, 'action' => ['name' => 'test']])
                ],
                Response::HTTP_BAD_REQUEST
            ],
            [
                [
                    'POST',
                    'application/json',
                    json_encode(['input' => [], 'session_variables' => [], 'action' => null])
                ],
                Response::HTTP_BAD_REQUEST
            ],
            [
                [
                    'POST',
                    'application/json',
                    ''
                ],
                Response::HTTP_BAD_REQUEST
            ],
            [
                [
                    'POST',
                    'application/json',
                    json_encode(['input' => [], 'session_variables' => [], 'action' => ['name' => 'test']])
                ],
                Response::HTTP_BAD_REQUEST
            ],
        ];
    }

}