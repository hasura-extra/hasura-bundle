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

class ApplyMetadataTest extends KernelTestCase
{
    use CleanupMetadataPathTrait;

    public function testApplyMetadata(): void
    {
        $kernel = self::bootKernel();
        $client = $kernel->getContainer()->get('hasura.api_client.client');
        $oldResourceVersion = $client->metadata()->export()['resource_version'];

        // export yaml files
        $exporter = new CommandTester((new Application($kernel))->find('hasura:metadata:export'));
        $exporter->execute(['--force']);

        // apply
        $tester = new CommandTester((new Application($kernel))->find('hasura:metadata:apply'));
        $tester->execute([]);

        $currentResourceVersion = $client->metadata()->export()['resource_version'];

        $this->assertSame($oldResourceVersion + 1, $currentResourceVersion);
        $this->assertStringContainsString('Applying...', $tester->getDisplay());
        $this->assertStringContainsString('Done!', $tester->getDisplay());
    }

    public function testApplyNothing()
    {
        $kernel = self::bootKernel();
        // apply
        $tester = new CommandTester((new Application($kernel))->find('hasura:metadata:apply'));
        $tester->execute([]);

        $this->assertSame(3, $tester->getStatusCode());
        $this->assertStringContainsString('Not found metadata files.', $tester->getDisplay());

        $tester->execute(['--allow-no-metadata' => true]);
        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('No metadata files to apply.', $tester->getDisplay());
    }
}
