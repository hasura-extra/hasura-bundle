<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Functional\Maker;

use Hasura\Tests\TestKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class MakeEventHandlerTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        $dir =  __DIR__ . '/../../.kernel/temp/app/HasuraEvent';
        (new Filesystem())->remove($dir);
    }

    /**
     * @dataProvider inputDataProvider
     */
    public function testMake(
        array $commandInput,
        array $userInput,
        ?string $expectedHandler
    ): void {
        /** @var TestKernel $kernel */
        $kernel = self::bootKernel();
        $dir = $kernel->getTempDir() . '/app/HasuraEvent/Test';
        $this->assertDirectoryDoesNotExist($dir);

        $tester = new CommandTester((new Application($kernel))->find('make:event-handler'));
        $tester->setInputs($userInput);
        $tester->execute($commandInput);

        $this->assertFileExists($dir . '/Handler.php');
        $this->assertSame($expectedHandler, file_get_contents($dir . '/Handler.php'));

        $display = $tester->getDisplay();
        $this->assertStringContainsString('Success!', $display);

        if (!isset($commandInput['event'])) {
            $this->assertStringContainsString('Your event name (ex: productInserted)', $display);
        } else {
            $this->assertStringNotContainsString('Your event name (ex: productInserted)', $display);
        }
    }

    public function inputDataProvider(): \Generator
    {
        $expectedHandler = <<<'EXPECTED'
<?php

namespace App\HasuraEvent\Test;

use Hasura\Attribute\AsEventHandler;
use Hasura\Handler\EventHandlerInterface;

#[AsEventHandler(name: 'Test')]
final class Handler implements EventHandlerInterface
{
    /**
    * {@inheritdoc}
    */
    public function handle(string $name, string $id, array $table, array $event, string $createdAt, array $deliveryInfo): void
    {
        // Handle event logic...
    }
}
EXPECTED;
        yield [['event' => 'Test'], [], $expectedHandler];
        yield [[], ['event' => 'Test'], $expectedHandler];
    }
}
