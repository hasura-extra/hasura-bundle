<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\Fixture;

use Symfony\Component\HttpFoundation\Request;
use VXM\Hasura\Attribute\AsEventHandler;
use VXM\Hasura\Handler\EventHandlerInterface;

#[AsEventHandler(name: 'test')]
final class EventHandler implements EventHandlerInterface
{
    private Request $requestTest;

    public function handle(
        string $name,
        string $id,
        array $table,
        array $event,
        string $createdAt,
        array $deliveryInfo
    ): void {
        $this->requestTest->attributes->set(
            '_event_handled',
            [
                $name,
                $id,
                $table,
                $event,
                $createdAt,
                $deliveryInfo
            ]
        );
    }

    public function setRequestTest(Request $request)
    {
        $this->requestTest = $request;
    }
}
