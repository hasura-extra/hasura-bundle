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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use VXM\Hasura\Handler\ActionHandlerInterface;
use VXM\Hasura\Handler\EventHandlerInterface;
use VXM\Hasura\Handler\HandlerDescriptor;
use VXM\Hasura\Tests\Fixture\ActionHandler;
use VXM\Hasura\Tests\Fixture\EventHandler;

/**
 * @mixin TestCase
 */
trait ResolvedRequestEventTrait
{
    private function getRequestEvent(
        string $type,
        array $handlerAttributes = []
    ): RequestEvent {
        $request = Request::create('/');
        $request->attributes->set('_hasura', $type);
        $request->attributes->set(
            '_hasura_handler_descriptor',
            new HandlerDescriptor(
                $this->getHandler($type, $request),
                $handlerAttributes
            )
        );

        return new RequestEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
    }

    private function getViewEvent(
        string $type,
        mixed $controllerResult,
        array $handlerAttributes = []
    ): ViewEvent {
        $request = Request::create('/');
        $request->attributes->set('_hasura', $type);
        $request->attributes->set(
            '_hasura_handler_descriptor',
            new HandlerDescriptor(
                $this->getHandler($type, $request),
                $handlerAttributes
            )
        );
        $request->attributes->set('_hasura_request_data', []);

        return new ViewEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $controllerResult
        );
    }

    private function getHandler(string $type, Request $request): ActionHandler|EventHandler
    {
        $handler = 'action' === $type ? new ActionHandler() : new EventHandler();
        $handler->setRequestTest($request);

        return $handler;
    }
}