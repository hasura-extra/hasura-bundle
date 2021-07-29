<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Service\Metadata;

use Hasura\ApiClient\Client;
use Symfony\Component\Filesystem\Filesystem;

final class Manager implements ManagerInterface
{
    public function __construct(
        private Client $apiClient,
        Filesystem $filesystem,
        private string $metadataPath,
        private Exporter $exporter,
        private FileExportedParser $fileExportedParser
    ) {
        $filesystem->mkdir($this->metadataPath);

        if (!is_writable($this->metadataPath)) {
            throw new \InvalidArgumentException('Metadata path should have write permission.');
        }
    }

    public function export(bool $force): void
    {
        $this->exporter->export($this->metadataPath, $force);
    }

    public function apply(bool $allowInconsistency = false): void
    {
        $metadata = $this->fileExportedParser->parse($this->metadataPath);

        $this->apiClient->metadata()->replace($metadata, $allowInconsistency);
    }

    public function reload(bool $reloadRemoteSchemas = true, bool $reloadSources = true): void
    {
        $this->apiClient->metadata()->reload($reloadRemoteSchemas, $reloadSources);
    }

    public function clear(): void
    {
        $this->apiClient->metadata()->clear();
    }

    public function getInconsistentMetadata(): array
    {
        return $this->apiClient->metadata()->getInconsistentMetadata();
    }

    public function dropInconsistentMetadata(): void
    {
        $this->apiClient->metadata()->dropInconsistentMetadata();
    }
}