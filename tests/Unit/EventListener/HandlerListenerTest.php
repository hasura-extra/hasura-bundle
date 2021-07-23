<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\Unit\EventListener;

use PHPUnit\Framework\TestCase;
use VXM\Hasura\EventListener\HandlerListener;

class HandlerListenerTest extends TestCase
{
    use ResolvedRequestEventTrait;

    /**
     * @dataProvider handleRequestProvider
     */
    public function testHandleValidRequest(
        string $type,
        array $requestData,
        array $handlerAttributes,
        array $expectedSetBackData
    ) {
        $event = $this->getRequestEvent($type, $handlerAttributes);
        $attributes = $event->getRequest()->attributes;
        $attributes->set('_hasura_request_data', $requestData);
        $listener = new HandlerListener();
        $listener->onKernelRequest($event);

        $this->assertSame($expectedSetBackData, $attributes->get("_{$type}_handled"));
    }

    public function handleRequestProvider(): array
    {
        return [
            [
                'action', //type
                ['input' => [3, 2, 1], 'session_variables' => [4, 5, 6]], //request data
                ['name' => 'action'], // handler attributes
                ['action', [3, 2, 1], [4, 5, 6]] // expected handled set back data
            ],
            [
                'action',
                ['input' => [1, 2, 3], 'session_variables' => [6, 5, 4]],
                ['name' => 'action 2'],
                ['action 2', [1, 2, 3], [6, 5, 4]]
            ],
            [
                'event',
                ['id' => '1', 'table' => [2], 'event' => [3], 'created_at' => '4', 'delivery_info' => [5]],
                ['name' => 'event'],
                ['event', '1', [2], [3], '4', [5]]
            ],
            [
                'event',
                ['id' => '5', 'table' => [4], 'event' => [3], 'created_at' => '2', 'delivery_info' => [1]],
                ['name' => 'event 2'],
                ['event 2', '5', [4], [3], '2', [1]]
            ],
        ];
    }
}