<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use VXM\Hasura\Action\Action;
use VXM\Hasura\Action\ActionManager;
use VXM\Hasura\Action\Metadata;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('vxm.hasura.action_manager', ActionManager::class)
            ->args(
                [
                    abstract_arg('actions'),
                ]
            )
        ->set('vxm.hasura.action_metadata', Metadata::class)
            ->args(
                [
                    abstract_arg('name'),
                    abstract_arg('input class'),
                    abstract_arg('denormalize context'),
                    abstract_arg('validate input'),
                    abstract_arg('normalize context')
                ]
            )
        ->set('vxm.hasura.action', Action::class)
            ->abstract()
            ->args(
                [
                    abstract_arg('metadata'),
                    abstract_arg('resolver')
                ]
            )
    ;
};