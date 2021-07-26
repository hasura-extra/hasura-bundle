<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use function Symfony\Component\String\u;

final class MakeEventHandler extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:event-handler';
    }

    public static function getCommandDescription()
    {
        return 'Make Hasura event handler';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command->addArgument(
            'event',
            InputArgument::OPTIONAL,
            'Your event name (ex: productInserted)'
        );
        $command->setHelp(file_get_contents(__DIR__ . '/Resources/help/MakeEventHandler.txt'));
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (null === $input->getArgument('event')) {
            $arg = $command->getDefinition()->getArgument('event');
            $question = new Question($arg->getDescription());
            $value = $io->askQuestion($question);

            $input->setArgument('event', $value);
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $event = $input->getArgument('event');
        $namespacePrefix = sprintf('HasuraEvent\\%s\\', u($event)->title()->toString());
        $classDetail = $generator->createClassNameDetails(
            'Handler',
            $namespacePrefix,
        );
        $generator->generateClass(
            $classDetail->getFullName(),
            __DIR__ . '/Resources/skeleton/EventHandler.tpl.php',
            [
                'event' => $event,
            ]
        );

        $generator->writeChanges();
        $this->writeSuccessMessage($io);
        $io->text('Next: Open your event handler class and write handle logic.');
    }
}
