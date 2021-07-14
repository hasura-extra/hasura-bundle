<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

return static function (RoutingConfigurator $configurator) {
    $configurator
        ->add('vxm.hasura.controller.action', '/hasura_action')
            ->controller('vxm.hasura.controller.action_placeholder')
            ->defaults(['_hasura' => 'action'])
            ->methods(['POST'])
        ->add('vxm.hasura.controller.event_trigger', '/hasura_event_trigger')
            ->controller('vxm.hasura.controller.event_trigger_placeholder')
            ->defaults(['_hasura' => 'event_trigger'])
            ->methods(['POST'])
    ;
};
