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

class DropInconsistentMetadataTest extends KernelTestCase
{
    use BackupAndRestoreMetadataTrait;

    public function testDropInconsistentMetadata(): void
    {
        $kernel = self::bootKernel();
        $client = $kernel->getContainer()->get('hasura.api_client.client');
        $metadataInconsistent = $this->backupMetadata;
        $metadataInconsistent['sources'][0]['tables'][] = [
            'table' => [
                'schema' => 'public',
                'name' => 'inconsistent_table',
            ],
        ];

        $client->metadata()->replace($metadataInconsistent, true);

        $metadata = $client->metadata()->export()['metadata'];

        $this->assertTrue(
            in_array(
                'inconsistent_table',
                array_column(array_column($metadata['sources'][0]['tables'], 'table'), 'name')
            )
        );

        $tester = new CommandTester((new Application($kernel))->find('hasura:metadata:drop-inconsistent'));
        $tester->execute([]);

        $metadata = $client->metadata()->export()['metadata'];

        $this->assertFalse(
            in_array(
                'inconsistent_table',
                array_column(array_column($metadata['sources'][0]['tables'], 'table'), 'name')
            )
        );

        $this->assertStringContainsString('Dropping...', $tester->getDisplay());
        $this->assertStringContainsString('Done!', $tester->getDisplay());
    }
}
