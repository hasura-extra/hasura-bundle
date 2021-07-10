<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use VXM\Hasura\EventListener\Action\ActionInputValidateListener;
use VXM\Hasura\EventListener\Action\ActionInputValidationExceptionListener;
use VXM\Hasura\EventListener\Action\DenormalizeActionInputListener;
use VXM\Hasura\EventListener\Action\NormalizeActionOutputListener;
use VXM\Hasura\EventListener\Action\ResolveActionListener;
use VXM\Hasura\EventListener\EventTrigger\HandleEventListener;
use VXM\Hasura\EventListener\NormalizeRequestListener;
use VXM\Hasura\EventListener\RespondListener;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('vxm.hasura.event_listener.normalize_request', NormalizeRequestListener::class)
            ->args(
                [
                    service('vxm.hasura.validation.chain_request_validator'),
                    service('vxm.hasura.action.manager'),
                    service('vxm.hasura.event_trigger.manager')
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
                    service('vxm.hasura.serializer')
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
                    service('vxm.hasura.validator')
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
        ->set('vxm.hasura.event_listener.resolve_action', ResolveActionListener::class)
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.request',
                    'method' => 'onKernelRequest',
                    'priority' => 2,
                ]
            )
        ->set('vxm.hasura.event_listener.handle_event', HandleEventListener::class)
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
                    service('vxm.hasura.serializer')
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
        ->set('vxm.hasura.event_listener.normalize_action_output', RespondListener::class)
            ->tag(
                'kernel.event_listener',
                [
                    'event' => 'kernel.view',
                    'method' => 'onKernelView',
                    'priority' => 8,
                ]
            )
        ->set('vxm.hasura.event_listener.action_input_validation_exception', ActionInputValidationExceptionListener::class)
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