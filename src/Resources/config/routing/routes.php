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
        ->add('hasura.controller.action', '/hasura_action')
            ->controller('hasura.controller.action_placeholder')
            ->defaults(['_hasura' => 'action'])
            ->methods(['POST'])
        ->add('hasura.controller.event', '/hasura_event')
            ->controller('hasura.controller.event_placeholder')
            ->defaults(['_hasura' => 'event'])
            ->methods(['POST'])
    ;
};
