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
use Symfony\Component\Console\Tester\CommandTester;

trait BackupAndRestoreMetadataTrait
{
    use CleanupMetadataPathTrait {
        tearDown as tearDownCleanup;
    }

    private ?array $backupMetadata = null;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();
        $exporter = new CommandTester((new Application($kernel))->find('hasura:metadata:export'));
        $exporter->execute(['--force']);
    }

    protected function tearDown(): void
    {
        $kernel = self::bootKernel();
        $inconsistentDropper = new CommandTester((new Application($kernel))->find('hasura:metadata:drop-inconsistent'));
        $applier = new CommandTester((new Application($kernel))->find('hasura:metadata:apply'));

        $inconsistentDropper->execute([]);
        $applier->execute([]);
        $this->tearDownCleanup();
    }
}
