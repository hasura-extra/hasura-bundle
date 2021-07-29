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
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

final class ApplyMetadata extends BaseMetadataCommand
{
    protected static $defaultName = 'hasura:metadata:apply';

    protected static $defaultDescription = 'Apply Hasura metadata';

    protected function configure()
    {
        parent::configure();

        $this->addOption('allow-inconsistency', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->section('Applying...');

        try {
            $this->metadataManager->apply($input->getOption('allow-inconsistency'));
            $this->io->section('Done!');
        } catch (HttpExceptionInterface $exception) {
            $this->io->section($exception->getMessage());
            $this->io->error('Please check your Hasura server configuration.');

            return 1;
        } catch (ParseException $exception) {
            $this->io->section(sprintf('Can not parse metadata file: `%s`', $exception->getParsedFile()));
            $this->io->error($exception->getMessage());

            return 2;
        }

        return 0;
    }
}
