<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Action\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ValidRequestTest extends WebTestCase
{
    public function testActionHaveInputClass(): void
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/hasura_action',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(
                [
                    'input' => ['test' => 'not blank'],
                    'session_variables' => [],
                    'action' => ['name' => 'test'],
                ]
            )
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame($client->getResponse()->getContent(), json_encode(['test' => 'test']));
    }

    public function testActionWithoutInputClass(): void
    {
        $client = $this->createClient();
        $client->request(
            'POST',
            '/hasura_action',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(
                [
                    'input' => ['test' => 'not blank'],
                    'session_variables' => [],
                    'action' => ['name' => 'testWithoutInputClass'],
                ]
            )
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame($client->getResponse()->getContent(), json_encode(['test' => 'not blank']));
    }
}
