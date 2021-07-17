<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use VXM\Hasura\EventListener\ActionInputListener;
use VXM\Hasura\EventListener\ActionOutputListener;
use VXM\Hasura\EventListener\EventPriorities;
use VXM\Hasura\EventListener\ExceptionListener;
use VXM\Hasura\EventListener\HandlerListener;
use VXM\Hasura\EventListener\ResolveRequestListener;
use VXM\Hasura\EventListener\RespondListener;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('vxm.hasura.event_listener.resolve_request', ResolveRequestListener::class)
            ->args(
                [
                    service('vxm.hasura.validation.chain_request_validator'),
                    service('vxm.hasura.handler.locator'),
                ]
            )
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.request',
                    'method' => 'onKernelRequest',
                    'priority' => EventPriorities::PRE_RESOLVE_REQUEST - 1,
                ]
            )
        ->set('vxm.hasura.event_listener.action_input', ActionInputListener::class)
            ->args(
                [
                    service('vxm.hasura.serializer'),
                    service('vxm.hasura.validator'),
                ]
            )
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.request',
                    'method' => 'onKernelRequest',
                    'priority' => EventPriorities::PRE_ACTION_INPUT - 1,
                ]
            )
        ->set('vxm.hasura.event_listener.handler', HandlerListener::class)
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.request',
                    'method' => 'onKernelRequest',
                    'priority' => EventPriorities::PRE_HANDLER - 1,
                ]
            )
        ->set('vxm.hasura.event_listener.action_output', ActionOutputListener::class)
            ->args(
                [
                    service('vxm.hasura.serializer'),
                ]
            )
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.view',
                    'method' => 'onKernelView',
                    'priority' => EventPriorities::PRE_ACTION_OUTPUT - 1,
                ]
            )
        ->set('vxm.hasura.event_listener.respond', RespondListener::class)
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.view',
                    'method' => 'onKernelView',
                    'priority' => EventPriorities::PRE_RESPOND - 1,
                ]
            )
        ->set('vxm.hasura.event_listener.exception', ExceptionListener::class)
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.exception',
                    'method' => 'onKernelException',
                    'priority' => -16,
                ]
            )
    ;
};
