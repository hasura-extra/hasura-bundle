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
use function Symfony\Component\String\u;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symfony\Component\Yaml\Yaml;

final class Exporter
{
    public function __construct(private Client $client, private Filesystem $filesystem)
    {
    }

    public function export(string $metadataPath, bool $force = false): void
    {
        if ($force) {
            $this->filesystem->remove($metadataPath);
            $this->filesystem->mkdir($metadataPath);
        }

        $data = $this->client->metadata()->export();
        $metadata = $data['metadata'];

        foreach ($metadata as $field => $value) {
            if (isset(FileExport::FIELDS_MAPPING[$field])) {
                $value = $this->normalizeEmptyObjectFieldValue($field, $value);
                $exportMethod = u('export_' . $field)->camel()->toString();
                $this->{$exportMethod}($value, $metadataPath);
            }
        }
    }

    private function normalizeEmptyObjectFieldValue(string $field, mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        static $objectFieldPatterns = [
            '~^sources\.\d+\.tables\.\d+\.(select|insert|update|delete)_permissions\.\d+\.permission\.(check|filter)$~',
        ];

        foreach ($value as $childField => &$childValue) {
            $childValue = $this->normalizeEmptyObjectFieldValue(
                sprintf('%s.%s', $field, $childField),
                $childValue
            );
        }

        if (!empty($value)) {
            return $value;
        }

        foreach ($objectFieldPatterns as $pattern) {
            if (preg_match($pattern, $field)) {
                $value = new \ArrayObject($value);
                break;
            }
        }

        return $value;
    }

    private function exportSources(array $sources, string $basePath): void
    {
        $exported = [];

        foreach ($sources as $source) {
            $sourcePath = sprintf('sources/%s/tables', $source['name']);
            $collectionFile = sprintf('%s.yaml', $sourcePath);

            $this->exportItems(
                $source['tables'],
                fn(array $table) => sprintf(
                    '%s_%s.yaml',
                    u($table['table']['schema'])->snake()->toString(),
                    u($table['table']['name'])->snake()->toString()
                ),
                $collectionFile,
                $sourcePath,
                $basePath
            );

            $source['tables'] = $this->createIncludeTaggedValue($collectionFile);
            $exported[] = $source;
        }

        $this->filesystem->dumpFile(
            sprintf('%s/%s', $basePath, FileExport::SOURCES),
            $this->yamlDump($exported)
        );
    }

    private function exportActions(array $actions, string $basePath): void
    {
        $this->exportItems(
            $actions,
            fn(array $item) => sprintf('%s.yaml', u($item['name'])->snake()->toString()),
            FileExport::ACTIONS,
            'actions',
            $basePath
        );
    }

    private function exportVersion(int $version, string $basePath): void
    {
        $this->filesystem->dumpFile(
            sprintf('%s/%s', $basePath, FileExport::VERSION),
            $this->yamlDump($version)
        );
    }

    private function exportCustomTypes(array $customTypes, string $basePath): void
    {
        $exported = [];

        foreach ($customTypes as $type => $items) {
            $typePath = sprintf('custom_types/%s', $type);
            $collectionFilePath = sprintf('%s.yaml', $typePath);
            $exported[$type] = $this->createIncludeTaggedValue($collectionFilePath);

            $this->exportItems(
                $items,
                fn(array $item) => sprintf('%s.yaml', u($item['name'])->snake()->toString()),
                $collectionFilePath,
                $typePath,
                $basePath
            );
        }

        $this->filesystem->dumpFile(
            sprintf('%s/%s', $basePath, FileExport::CUSTOM_TYPES),
            $this->yamlDump($exported)
        );
    }

    private function exportCronTriggers(array $cronTriggers, string $basePath): void
    {
        $this->exportItems(
            $cronTriggers,
            fn(array $item) => sprintf('%s.yaml', u($item['name'])->snake()->toString()),
            FileExport::CRON_TRIGGERS,
            'cron_triggers',
            $basePath
        );
    }

    private function exportRemoteSchemas(array $remoteSchemas, string $basePath): void
    {
        $this->exportItems(
            $remoteSchemas,
            fn(array $item) => sprintf('%s.yaml', u($item['name'])->snake()->toString()),
            FileExport::REMOTE_SCHEMAS,
            'remote_schemas',
            $basePath
        );
    }

    private function exportRestEndpoints(array $restEndpoints, string $basePath): void
    {
        $this->exportItems(
            $restEndpoints,
            fn(array $item) => sprintf('%s.yaml', u($item['name'])->snake()->toString()),
            FileExport::REST_ENDPOINTS,
            'rest_endpoints',
            $basePath
        );
    }

    private function exportAllowlist(array $allowList, string $basePath): void
    {
        $this->filesystem->dumpFile(
            sprintf(
                '%s/%s',
                $basePath,
                FileExport::ALLOW_LIST
            ),
            $this->yamlDump($allowList)
        );
    }

    private function exportInheritedRoles(array $inheritedRoles, string $basePath): void
    {
        $this->exportItems(
            $inheritedRoles,
            fn(array $item) => sprintf('%s.yaml', u($item['role_name'])->snake()->toString()),
            FileExport::INHERITED_ROLES,
            'inherited_roles',
            $basePath
        );
    }

    private function exportQueryCollections(array $queryCollections, string $basePath): void
    {
        $this->exportItems(
            $queryCollections,
            fn(array $item) => sprintf('%s.yaml', u($item['name'])->snake()->toString()),
            FileExport::QUERY_COLLECTION,
            'query_collections',
            $basePath
        );
    }

    private function exportItems(
        array $items,
        callable $itemNameGenerator,
        string $collectionFile,
        string $itemsPath,
        string $basePath
    ): void {
        $itemsPath = sprintf('%s/%s', $basePath, $itemsPath);
        $collectionFile = sprintf('%s/%s', $basePath, $collectionFile);
        $exported = [];

        $this->filesystem->mkdir($itemsPath);

        foreach ($items as $item) {
            $file = $itemNameGenerator($item);
            $relativePath = rtrim($this->filesystem->makePathRelative($itemsPath, dirname($collectionFile)), '/');
            $relativeFilePath = sprintf('%s/%s', $relativePath, $file);
            $exported[] = $this->createIncludeTaggedValue($relativeFilePath);

            $this->filesystem->dumpFile(
                sprintf('%s/%s', $itemsPath, $file),
                $this->yamlDump($item)
            );
        }

        $this->filesystem->dumpFile(
            $collectionFile,
            $this->yamlDump($exported)
        );
    }

    private function yamlDump(mixed $data): string
    {
        $flags = Yaml::DUMP_NULL_AS_TILDE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE | Yaml::DUMP_OBJECT_AS_MAP;

        return Yaml::dump($data, 10, flags: $flags);
    }

    private function createIncludeTaggedValue(string $file): TaggedValue
    {
        return new TaggedValue('include', $file);
    }
}
