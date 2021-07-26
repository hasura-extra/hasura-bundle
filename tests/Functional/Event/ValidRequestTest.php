<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\Event\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ValidRequestTest extends WebTestCase
{
    public function testValidRequest(): void
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/hasura_event',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(
                [
                    'trigger' => [
                        'name' => 'test'
                    ],
                    'id' => '123e4567-e89b-12d3-a456-556642440000',
                    'table' => ['schema' => 'public', 'name' => 'abc'],
                    'event' => [
                        'op' => 'UPDATE',
                        'data' => [
                            'old' => ['test' => null],
                            'new' => ['test' => 'test']
                        ],
                        'session_variables' => [],
                        'trace_context' => null
                    ],
                    'delivery_info' => ['max_retries' => 0, 'current_retry' => 0],
                    'created_at' => '1'
                ]
            )
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame('[]', $client->getResponse()->getContent());
    }
}