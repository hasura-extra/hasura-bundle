<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Hasura\Command\ApplyMetadata;
use Hasura\Command\BaseMetadataCommand;
use Hasura\Command\ClearMetadata;
use Hasura\Command\DropInconsistentMetadata;
use Hasura\Command\ExportMetadata;
use Hasura\Command\GetInconsistentMetadata;
use Hasura\Command\ReloadMetadata;
use Symfony\Component\BrowserKit\Response;

return static function (ContainerConfigurator $configurator) {
    $configurator
        ->services()
        ->set('hasura.command.base_metadata', BaseMetadataCommand::class)
            ->abstract()
            ->args([service('hasura.service.manager')])
        ->set('hasura.command.export_metadata', ExportMetadata::class)
            ->parent('hasura.command.base_metadata')
            ->tag('console.command')
        ->set('hasura.command.apply_metadata', ApplyMetadata::class)
            ->parent('hasura.command.base_metadata')
            ->tag('console.command')
        ->set('hasura.command.drop_inconsistent_metadata', DropInconsistentMetadata::class)
            ->parent('hasura.command.base_metadata')
            ->tag('console.command')
        ->set('hasura.command.get_inconsistent_metadata', GetInconsistentMetadata::class)
            ->parent('hasura.command.base_metadata')
            ->tag('console.command')
        ->set('hasura.command.clear_metadata', ClearMetadata::class)
            ->parent('hasura.command.base_metadata')
            ->tag('console.command')
        ->set('hasura.command.reload_metadata', ReloadMetadata::class)
            ->parent('hasura.command.base_metadata')
            ->tag('console.command')
    ;
};
