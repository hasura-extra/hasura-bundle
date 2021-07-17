<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use VXM\Hasura\EventListener\ActionInputValidateListener;
use VXM\Hasura\EventListener\DenormalizeActionInputListener;
use VXM\Hasura\EventListener\ExceptionListener;
use VXM\Hasura\EventListener\HandlerListener;
use VXM\Hasura\EventListener\NormalizeActionOutputListener;
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
                    'priority' => 16,
                ]
            )
        ->set('vxm.hasura.event_listener.denormalize_action_input', DenormalizeActionInputListener::class)
            ->args(
                [
                    service('vxm.hasura.serializer'),
                ]
            )
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.request',
                    'method' => 'onKernelRequest',
                    'priority' => 6,
                ]
            )
        ->set('vxm.hasura.event_listener.action_input_validate', ActionInputValidateListener::class)
            ->args(
                [
                    service('vxm.hasura.validator'),
                ]
            )
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.request',
                    'method' => 'onKernelRequest',
                    'priority' => 4,
                ]
            )
        ->set('vxm.hasura.event_listener.handler', HandlerListener::class)
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.request',
                    'method' => 'onKernelRequest',
                    'priority' => 2,
                ]
            )
        ->set('vxm.hasura.event_listener.normalize_action_output', NormalizeActionOutputListener::class)
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
                    'priority' => 16,
                ]
            )
        ->set('vxm.hasura.event_listener.respond', RespondListener::class)
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.view',
                    'method' => 'onKernelView',
                    'priority' => 8,
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
