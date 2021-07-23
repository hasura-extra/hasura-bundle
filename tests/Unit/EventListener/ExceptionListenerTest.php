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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use VXM\Hasura\EventListener\ExceptionListener;
use VXM\Hasura\Exception\HttpExceptionInterface;

class ExceptionListenerTest extends TestCase
{
    public function testCatchHttpException(): void
    {
        $event = $this->getEvent($this->getHttpException(400, 'test exception'));
        $listener = new ExceptionListener();
        $listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(json_encode(['message' => 'test exception', 'code' => '400']), $response->getContent());
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testNotCatchAnotherException(): void
    {
        $event = $this->getEvent(new \RuntimeException('test exception'));
        $listener = new ExceptionListener();
        $listener->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    private function getHttpException(int $statusCode, string $message): \Throwable
    {
        return new class($statusCode, $message) extends HttpException implements HttpExceptionInterface {
            public function getMessageCode(): string
            {
                return (string)$this->getStatusCode();
            }
        };
    }

    private function getEvent(\Throwable $throwable): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            $this->createStub(Request::class),
            HttpKernelInterface::MAIN_REQUEST,
            $throwable
        );
    }
}