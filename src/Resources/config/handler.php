<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use VXM\Hasura\Handler\HandlerDescriptor;
use VXM\Hasura\Handler\HandlersLocator;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('vxm.hasura.handler.locator', HandlersLocator::class)
            ->args(
                [
                    abstract_arg('action descriptors'),
                    abstract_arg('event descriptors'),
                ]
            )
        ->set('vxm.hasura.handler.descriptor', HandlerDescriptor::class)
            ->abstract()
            ->args(
                [
                    abstract_arg('handler'),
                    abstract_arg('attributes'),
                ]
            )
    ;
};
