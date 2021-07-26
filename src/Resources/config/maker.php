<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Hasura\Maker\MakeActionHandler;
use Hasura\Maker\MakeEventHandler;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
            ->set('vxm.hasura.maker.action_handler', MakeActionHandler::class)
                ->tag('maker.command')
            ->set('vxm.hasura.maker.event_handler', MakeEventHandler::class)
                ->tag('maker.command')
    ;
};
