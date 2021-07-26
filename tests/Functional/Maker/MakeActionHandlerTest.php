<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Maker\Functional;

use Hasura\Tests\TestKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class MakeActionHandlerTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        $kernel = self::bootKernel();
        $dir = $kernel->getTempDir() . '/app/HasuraAction/Test';
        (new Filesystem())->remove($dir);
    }

    /**
     * @dataProvider inputDataProvider
     */
    public function testMake(
        array $commandInput,
        array $userInput,
        ?string $expectedHandler,
        ?string $expectedInput,
        ?string $expectedOutput
    ): void {
        /** @var TestKernel $kernel */
        $kernel = self::bootKernel();
        $dir = $kernel->getTempDir() . '/app/HasuraAction/Test';
        $this->assertDirectoryDoesNotExist($dir);

        $tester = new CommandTester((new Application($kernel))->find('make:action-handler'));
        $tester->setInputs($userInput);
        $tester->execute($commandInput);

        $this->assertFileExists($dir . '/Handler.php');
        $this->assertSame($expectedHandler, file_get_contents($dir . '/Handler.php'));

        if (!isset($commandInput['--no-io'])) {
            $this->assertFileExists($dir . '/Input.php');
            $this->assertFileExists($dir . '/Output.php');
            $this->assertSame($expectedInput, file_get_contents($dir . '/Input.php'));
            $this->assertSame($expectedOutput, file_get_contents($dir . '/Output.php'));
        } else {
            $this->assertFileDoesNotExist($dir . '/Input.php');
            $this->assertFileDoesNotExist($dir . '/Output.php');
        }

        $display = $tester->getDisplay();
        $this->assertStringContainsString('Success!', $display);

        if (!isset($commandInput['action'])) {
            $this->assertStringContainsString('Your action name (ex: insertProduct)', $display);
        } else {
            $this->assertStringNotContainsString('Your action name (ex: insertProduct)', $display);
        }
    }

    public function inputDataProvider(): \Generator
    {
        $expectedHandler = <<<'EXPECTED'
<?php

namespace App\HasuraAction\Test;

use Hasura\Attribute\AsActionHandler;
use Hasura\Handler\ActionHandlerInterface;

#[AsActionHandler(name: 'Test', inputClass: Input::class)]
final class Handler implements ActionHandlerInterface
{
    /**
    * {@inheritdoc}
    */
    public function handle(string $action, array | object $input, array $sessionVariables): array | object
    {
        // Handle action logic...
    }
}
EXPECTED;
        $expectedInput = <<<'EXPECTED'
<?php

namespace App\HasuraAction\Test;

final class Input
{
}
EXPECTED;
        $expectedOutput = <<<'EXPECTED'
<?php

namespace App\HasuraAction\Test;

final class Output
{
}
EXPECTED;

        yield [['action' => 'Test'], [], $expectedHandler, $expectedInput, $expectedOutput];
        yield [[], ['action' => 'Test'], $expectedHandler, $expectedInput, $expectedOutput];

        $expectedHandler = <<<'EXPECTED'
<?php

namespace App\HasuraAction\Test;

use Hasura\Attribute\AsActionHandler;
use Hasura\Handler\ActionHandlerInterface;

#[AsActionHandler(name: 'Test')]
final class Handler implements ActionHandlerInterface
{
    /**
    * {@inheritdoc}
    */
    public function handle(string $action, array | object $input, array $sessionVariables): array | object
    {
        // Handle action logic...
    }
}
EXPECTED;

        yield [['action' => 'Test', '--no-io' => true], [], $expectedHandler, null, null];
    }
}
