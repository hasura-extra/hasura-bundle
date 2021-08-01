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

class ClearMetadataTest extends KernelTestCase
{
    use BackupAndRestoreMetadataTrait;

    public function testClearMetadata(): void
    {
        $kernel = self::bootKernel();
        $client = $kernel->getContainer()->get('hasura.api_client.client');
        // clear metadata
        $tester = new CommandTester((new Application($kernel))->find('hasura:metadata:clear'));
        $tester->execute([]);
        $this->assertStringContainsString('Clearing...', $tester->getDisplay());
        $this->assertStringContainsString('Done!', $tester->getDisplay());

        $metadata = $client->metadata()->export()['metadata'];

        $this->assertEmpty($metadata['sources'][0]['tables']);
    }
}
