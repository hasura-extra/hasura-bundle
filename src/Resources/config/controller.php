<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use VXM\Hasura\Controller\Placeholder;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('vxm.hasura.controller.placeholder', Placeholder::class)
            ->public()
        ->alias('vxm.hasura.controller.action_placeholder', 'vxm.hasura.controller.placeholder')
            ->public()
        ->alias('vxm.hasura.controller.event_placeholder', 'vxm.hasura.controller.placeholder')
            ->public()
    ;
};
