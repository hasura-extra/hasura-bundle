<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Hasura\Service\Exporter;
use Hasura\Service\FileExportedParser;
use Hasura\Service\Manager;
use Hasura\Service\ManagerInterface;
use Hasura\Service\SchemaFactory;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('hasura.service.manager', Manager::class)
            ->args(
                [
                    service('hasura.api_client.client'),
                    service('filesystem'),
                    param('hasura.metadata_path'),
                    service('hasura.service.exporter'),
                    service('hasura.service.file_exported_parser')
                ]
            )
        ->alias(ManagerInterface::class, 'hasura.service.manager')
        ->set('hasura.service.exporter', Exporter::class)
            ->args([service('hasura.api_client.client'), service('filesystem')])
        ->set('hasura.service.file_exported_parser', FileExportedParser::class)
            ->args([service('filesystem')])
        ->set('hasura.service.schema_factory', SchemaFactory::class)
            ->args([service('hasura.api_client.client')])
        ->alias(SchemaFactory::class, 'hasura.service.schema_factory')
    ;
};
