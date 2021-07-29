<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Functional\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ReloadMetadataTest extends KernelTestCase
{
    public function testReloadMetadata(): void
    {
        $kernel = self::bootKernel();

        $tester = new CommandTester((new Application($kernel))->find('hasura:metadata:reload'));
        $tester->execute([]);

        $this->assertStringContainsString('Reloading...', $tester->getDisplay());
        $this->assertStringContainsString('Done!', $tester->getDisplay());
    }
}
