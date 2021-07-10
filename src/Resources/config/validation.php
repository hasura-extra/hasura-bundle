<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use VXM\Hasura\Validation\AbstractRequestValidator;
use VXM\Hasura\Validation\ActionRequestValidator;
use VXM\Hasura\Validation\ChainRequestValidator;
use VXM\Hasura\Validation\EventTriggerRequestValidator;
use VXM\Hasura\Validation\RequestValidatorInterface;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('vxm.hasura.validation.request_validator', AbstractRequestValidator::class)
            ->abstract()
            ->args(
                [
                    service('vxm.hasura.validator')
                ]
            )
        ->set('vxm.hasura.validation.action_request_validator', ActionRequestValidator::class)
            ->parent('vxm.hasura.validation.request_validator')
        ->set('vxm.hasura.validation.event_trigger_request_validator', EventTriggerRequestValidator::class)
            ->parent('vxm.hasura.validation.request_validator')
        ->set('vxm.hasura.validation.chain_request_validator', ChainRequestValidator::class)
            ->args(
                [
                    iterator(
                        [
                            service('vxm.hasura.validation.action_request_validator'),
                            service('vxm.hasura.validation.event_trigger_request_validator'),
                        ]
                    ),
                ]
            )
        ->alias(RequestValidatorInterface::class, 'vxm.hasura.validation.chain_request_validator')
    ;
};