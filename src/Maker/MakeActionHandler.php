<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use function Symfony\Component\String\u;

final class MakeActionHandler extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:action-handler';
    }

    public static function getCommandDescription()
    {
        return 'Make Hasura action handler';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command->addArgument(
            'action',
            InputArgument::OPTIONAL,
            'Your action name (ex: insertProduct)'
        );
        $command->addOption(
            'no-io',
            mode: InputOption::VALUE_NONE,
            description: 'Do not generate input & output class for action handler'
        );
        $command->setHelp(file_get_contents(__DIR__ . '/Resources/help/MakeActionHandler.txt'));
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (null === $input->getArgument('action')) {
            $arg = $command->getDefinition()->getArgument('action');
            $question = new Question($arg->getDescription());
            $value = $io->askQuestion($question);

            $input->setArgument('action', $value);
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $namespacePrefix = sprintf('ActionHandler\\%s\\', u($input->getArgument('action'))->title()->toString());
        $classDetail = $generator->createClassNameDetails(
            'Handler',
            $namespacePrefix,
        );
        $ioOpt = !$input->getOption('no-io');

        $generator->generateClass(
            $classDetail->getFullName(),
            __DIR__ . '/Resources/skeleton/ActionHandler.tpl.php',
            [
                'io' => $ioOpt,
            ]
        );

        if ($ioOpt) {
            foreach (['Input', 'Output'] as $name) {
                $ioClassDetail = $generator->createClassNameDetails(
                    $name,
                    $namespacePrefix,
                );

                $generator->generateClass(
                    $ioClassDetail->getFullName(),
                    __DIR__ . '/Resources/skeleton/ActionIO.tpl.php'
                );
            }
        }

        $generator->writeChanges();
        $this->writeSuccessMessage($io);
        $io->text('Next: Open your action handler class and write handle logic.');
    }
}
