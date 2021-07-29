<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Hasura\Service\Metadata\Applier;
use Hasura\Service\Metadata\Exporter;
use Hasura\Service\Metadata\FileExportedParser;
use Hasura\Service\Metadata\Manager;
use Hasura\Service\Metadata\ManagerInterface;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('hasura.service.metadata.manager', Manager::class)
            ->args(
                [
                    service('hasura.api_client.client'),
                    service('filesystem'),
                    param('hasura.metadata_path'),
                    service('hasura.service.metadata.exporter'),
                    service('hasura.service.metadata.file_exported_parser')
                ]
            )
        ->alias(ManagerInterface::class, 'hasura.service.metadata.manager')
        ->set('hasura.service.metadata.exporter', Exporter::class)
            ->args([service('hasura.api_client.client'), service('filesystem')])
        ->set('hasura.service.metadata.file_exported_parser', FileExportedParser::class)
            ->args([service('filesystem')])
    ;
};
