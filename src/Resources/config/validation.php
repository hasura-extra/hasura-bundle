<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Hasura\Validation\AbstractRequestValidator;
use Hasura\Validation\ActionRequestValidator;
use Hasura\Validation\ChainRequestValidator;
use Hasura\Validation\EventRequestValidator;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('hasura.validation.request_validator', AbstractRequestValidator::class)
            ->abstract()
            ->args(
                [
                    service('hasura.validator'),
                ]
            )
        ->set('hasura.validation.action_request_validator', ActionRequestValidator::class)
            ->parent('hasura.validation.request_validator')
        ->set('hasura.validation.event_request_validator', EventRequestValidator::class)
            ->parent('hasura.validation.request_validator')
        ->set('hasura.validation.chain_request_validator', ChainRequestValidator::class)
            ->args(
                [
                    iterator(
                        [
                            service('hasura.validation.action_request_validator'),
                            service('hasura.validation.event_request_validator'),
                        ]
                    ),
                ]
            )
    ;
};
