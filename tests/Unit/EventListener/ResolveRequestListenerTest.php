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
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\Validation;
use VXM\Hasura\EventListener\ResolveRequestListener;
use VXM\Hasura\Handler\HandlerDescriptor;
use VXM\Hasura\Handler\HandlersLocator;
use VXM\Hasura\Tests\Fixture\ActionHandler;
use VXM\Hasura\Tests\Fixture\EventHandler;
use VXM\Hasura\Validation\ActionRequestValidator;
use VXM\Hasura\Validation\ChainRequestValidator;
use VXM\Hasura\Validation\EventRequestValidator;

class ResolveRequestListenerTest extends TestCase
{
    public function testNormalRequest()
    {
        $listener = $this->getListener();
        $event = new RequestEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/'),
            HttpKernelInterface::MAIN_REQUEST
        );
        $listener->onKernelRequest($event);

        $this->assertEmpty($event->getRequest()->attributes->all());
    }

    /**
     * @dataProvider validRequestProvider
     */
    public function testValidRequest(
        string $type,
        string $contentType,
        string $requestContent
    ) {
        $listener = $this->getListener();
        $event = $this->createRequestEvent($type, $contentType, $requestContent);
        $listener->onKernelRequest($event);

        $this->assertSame(
            json_decode($requestContent, true),
            $event->getRequest()->attributes->get('_hasura_request_data')
        );
    }

    /**
     * @dataProvider invalidRequestProvider
     */
    public function testInvalidRequest(
        string $type,
        string $contentType,
        string $requestContent
    ) {
        $this->expectException(HttpExceptionInterface::class);
        $listener = $this->getListener();
        $event = $this->createRequestEvent($type, $contentType, $requestContent);
        $listener->onKernelRequest($event);
    }

    private function getListener(): ResolveRequestListener
    {
        $validator = Validation::createValidator();
        $actionHandlerDescriptor = new HandlerDescriptor(
            new ActionHandler(),
            ['name' => 'test']
        );
        $eventHandlerDescriptor = new HandlerDescriptor(
            new EventHandler(),
            ['name' => 'test']
        );

        return new ResolveRequestListener(
            new ChainRequestValidator(
                [
                    new ActionRequestValidator($validator),
                    new EventRequestValidator($validator)
                ]
            ),
            new HandlersLocator(['test' => $actionHandlerDescriptor], ['test' => $eventHandlerDescriptor])
        );
    }

    private function createRequestEvent(string $type, string $contentType, string $content): RequestEvent
    {
        $request = Request::create('/', server: ['HTTP_CONTENT_TYPE' => $contentType], content: $content);
        $request->attributes->set('_hasura', $type);

        return new RequestEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
    }

    public function validRequestProvider(): array
    {
        return [
            [
                'action',
                'application/json',
                json_encode(['input' => [], 'session_variables' => [], 'action' => ['name' => 'test']])
            ],
            [
                'event',
                'application/json',
                json_encode(
                    [
                        'trigger' => [
                            'name' => 'test'
                        ],
                        'id' => '123e4567-e89b-12d3-a456-556642440000',
                        'table' => ['name' => 'test', 'schema' => 'public'],
                        'event' => [
                            'op' => 'UPDATE',
                            'session_variables' => [],
                            'data' => [
                                'old' => ['name' => ''],
                                'new' => ['name' => 'test']
                            ],
                            'trace_context' => null
                        ],
                        'created_at' => '3',
                        'delivery_info' => ['max_retries' => 0, 'current_retry' => 0]
                    ]
                )
            ]
        ];
    }

    public function invalidRequestProvider(): array
    {
        return [
            [
                'action',
                'application/json',
                json_encode(['input' => null, 'session_variables' => [], 'action' => ['name' => 'test']])
            ],
            [
                'event',
                'application/json',
                json_encode(
                    [
                        'id' => '1',
                    ]
                )
            ],
            [
                'action',
                'application/json',
                ''
            ],
            [
                'event',
                'application/json',
                ''
            ],
            [
                'action',
                'application/xml',
                json_encode(['input' => [], 'session_variables' => [], 'action' => ['name' => 'test']])
            ],
            [
                'event',
                'application/xml',
                json_encode(
                    [
                        'trigger' => [
                            'name' => 'test'
                        ],
                        'id' => '123e4567-e89b-12d3-a456-556642440000',
                        'table' => ['name' => 'test', 'schema' => 'public'],
                        'event' => [
                            'op' => 'UPDATE',
                            'session_variables' => [],
                            'data' => [
                                'old' => ['name' => ''],
                                'new' => ['name' => 'test']
                            ],
                            'trace_context' => null
                        ],
                        'created_at' => '3',
                        'delivery_info' => ['max_retries' => 0, 'current_retry' => 0]
                    ]
                )
            ],
            [
                'event',
                'application/json',
                json_encode(
                    [
                        'trigger' => null,
                        'id' => '',
                        'table' => [],
                        'event' => [],
                        'created_at' => '',
                        'delivery_info' => []
                    ]
                )
            ]
        ];
    }
}