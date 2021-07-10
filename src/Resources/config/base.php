<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->alias('vxm.hasura.validator', 'validator')
        ->alias('vxm.hasura.serializer', 'serializer')
    ;
};