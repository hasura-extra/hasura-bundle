<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ReloadMetadata extends BaseMetadataCommand
{
    protected static $defaultName = 'hasura:metadata:reload';

    protected static $defaultDescription = 'Reload Hasura metadata';

    protected function configure()
    {
        parent::configure();

        $this->addOption('no-reload-remote-schemas', InputOption::VALUE_NONE);
        $this->addOption('no-reload-sources', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->section('Reloading...');

        $this->metadataManager->reload(
            !$input->getOption('no-reload-remote-schemas'),
            !$input->getOption('no-reload-sources')
        );

        $this->io->section('Done!');

        return 0;
    }
}
