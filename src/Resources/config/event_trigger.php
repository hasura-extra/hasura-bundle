<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use VXM\Hasura\EventTrigger\Metadata;
use VXM\Hasura\EventTrigger\EventTrigger;
use VXM\Hasura\EventTrigger\EventTriggerManager;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('vxm.hasura.event_trigger_manager', EventTriggerManager::class)
        ->args(
            [
                abstract_arg('event triggers'),
            ]
        )
        ->set('vxm.hasura.event_trigger_metadata', Metadata::class)
        ->args(
            [
                abstract_arg('trigger name'),
            ]
        )
        ->set('vxm.hasura.event_trigger', EventTrigger::class)
        ->abstract()
        ->args(
            [
                abstract_arg('metadata'),
                abstract_arg('handler')
            ]
        )
    ;
};