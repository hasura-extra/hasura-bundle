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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use VXM\Hasura\EventListener\RespondListener;

class RespondListenerTest extends TestCase
{
    use ResolvedRequestEventTrait;

    /**
     * @dataProvider controllerResultProvider
     */
    public function testCanSetResponseByControllerResult(
        string $type,
        mixed $controllerResult,
        string $expectedResponseContent,
        string $expectedInstanceOf
    ): void {
        $event = $this->getViewEvent($type, $controllerResult);
        $listener = new RespondListener();
        $listener->onKernelView($event);

        $this->assertInstanceOf($expectedInstanceOf, $event->getResponse());
        $this->assertSame($expectedResponseContent, $event->getResponse()->getContent());
    }

    public function controllerResultProvider(): array
    {
        return [
            ['action', [1, 2, 3], json_encode([1, 2, 3]), JsonResponse::class],
            ['event', [1, 2, 3], json_encode([1, 2, 3]), JsonResponse::class],
            ['action', 'test', 'test', Response::class],
            ['event', 'test', 'test', Response::class],
        ];
    }
}