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

class GetInconsistentMetadataTest extends KernelTestCase
{
    use BackupAndRestoreMetadataTrait;

    public function testGetInconsistentMetadata(): void
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

        $tester = new CommandTester((new Application($kernel))->find('hasura:metadata:get-inconsistent'));
        $tester->execute([]);

        $this->assertStringContainsString('Getting...', $tester->getDisplay());
        $this->assertStringContainsString(
            'Inconsistent object: no such table/view exists in source: "inconsistent_table"',
            $tester->getDisplay()
        );
    }

    public function testGetConsistentMetadata(): void
    {
        $kernel = self::bootKernel();

        $tester = new CommandTester((new Application($kernel))->find('hasura:metadata:get-inconsistent'));
        $tester->execute([]);

        $this->assertStringContainsString('Getting...', $tester->getDisplay());
        $this->assertStringContainsString(
            'Current metadata is consistent with database sources!',
            $tester->getDisplay()
        );
    }
}
