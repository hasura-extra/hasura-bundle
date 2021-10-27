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

        foreach (FileExport::FIELDS_MAPPING as $field => $file) {
            if (!isset($metadata[$field])) {
                $this->filesystem->remove(sprintf('%s/%s', $metadataPath, $file));

                continue;
            }

            $value = $this->normalizeEmptyObjectFieldValue($field, $metadata[$field]);
            $exportMethod = u('export_' . $field)->camel()->toString();
            $this->{$exportMethod}($value, $metadataPath, $file);
        }
    }

    private function normalizeEmptyObjectFieldValue(string $field, mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        static $objectFieldPatterns = [
            '~^sources\.\d+\.tables\.\d+\.(select|insert|update|delete)_permissions\.\d+\.permission\.(check|filter)$~',
            '~^remote_schemas\.\d+\.definition\.customization\.(type_names|field_names\.\d+)\.mapping$~',
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

    private function exportSources(array $sources, string $basePath, string $file): void
    {
        $exported = [];

        foreach ($sources as $source) {
            $sourcePath = sprintf('sources/%s/tables', $source['name']);
            $collectionFile = sprintf('%s.yaml', $sourcePath);

            $this->exportItems(
                $source['tables'] ?? [],
                fn(array $table) => sprintf(
                    '%s_%s.yaml',
                    $this->snakeCase($table['table']['schema']),
                    $this->snakeCase($table['table']['name'])
                ),
                $collectionFile,
                $sourcePath,
                $basePath
            );

            $source['tables'] = $this->createIncludeTaggedValue($collectionFile);
            $exported[] = $source;
        }

        $this->filesystem->dumpFile(
            sprintf('%s/%s', $basePath, $file),
            $this->yamlDump($exported)
        );
    }

    private function exportActions(array $actions, string $basePath, string $file): void
    {
        $this->exportItems(
            $actions,
            fn(array $item) => sprintf('%s.yaml', $this->snakeCase($item['name'])),
            $file,
            'actions',
            $basePath
        );
    }

    private function exportVersion(int $version, string $basePath, string $file): void
    {
        $this->filesystem->dumpFile(
            sprintf('%s/%s', $basePath, $file),
            $this->yamlDump($version)
        );
    }

    private function exportCustomTypes(array $customTypes, string $basePath, string $file): void
    {
        $exported = [];

        foreach ($customTypes as $type => $items) {
            $typePath = sprintf('custom_types/%s', $type);
            $collectionFilePath = sprintf('%s.yaml', $typePath);
            $exported[$type] = $this->createIncludeTaggedValue($collectionFilePath);

            $this->exportItems(
                $items,
                fn(array $item) => sprintf('%s.yaml', $this->snakeCase($item['name'])),
                $collectionFilePath,
                $typePath,
                $basePath
            );
        }

        $this->filesystem->dumpFile(
            sprintf('%s/%s', $basePath, $file),
            $this->yamlDump($exported)
        );
    }

    private function exportCronTriggers(array $cronTriggers, string $basePath, string $file): void
    {
        $this->exportItems(
            $cronTriggers,
            fn(array $item) => sprintf('%s.yaml', $this->snakeCase($item['name'])),
            $file,
            'cron_triggers',
            $basePath
        );
    }

    private function exportRemoteSchemas(array $remoteSchemas, string $basePath, string $file): void
    {
        $exported = [];

        foreach ($remoteSchemas as $remoteSchema) {
            $sourcePath = sprintf('remote_schemas/%s/permissions', $remoteSchema['name']);
            $collectionFile = sprintf('%s.yaml', $sourcePath);

            $this->exportItems(
                $remoteSchema['permissions'] ?? [],
                fn(array $permission) => sprintf('role_%s.yaml', $this->snakeCase($permission['role'])),
                $collectionFile,
                $sourcePath,
                $basePath
            );

            $remoteSchema['permissions'] = $this->createIncludeTaggedValue($collectionFile);
            $exported[] = $remoteSchema;
        }

        $this->filesystem->dumpFile(
            sprintf('%s/%s', $basePath, $file),
            $this->yamlDump($exported)
        );
    }

    private function exportRestEndpoints(array $restEndpoints, string $basePath, string $file): void
    {
        $this->exportItems(
            $restEndpoints,
            fn(array $item) => sprintf('%s.yaml', $this->snakeCase($item['name'])),
            $file,
            'rest_endpoints',
            $basePath
        );
    }

    private function exportAllowlist(array $allowList, string $basePath, string $file): void
    {
        $this->filesystem->dumpFile(
            sprintf(
                '%s/%s',
                $basePath,
                $file
            ),
            $this->yamlDump($allowList)
        );
    }

    private function exportInheritedRoles(array $inheritedRoles, string $basePath, string $file): void
    {
        $this->exportItems(
            $inheritedRoles,
            fn(array $item) => sprintf('%s.yaml', $this->snakeCase($item['role_name'])),
            $file,
            'inherited_roles',
            $basePath
        );
    }

    private function exportQueryCollections(array $queryCollections, string $basePath, string $file): void
    {
        $this->exportItems(
            $queryCollections,
            fn(array $item) => sprintf('%s.yaml', $this->snakeCase($item['name'])),
            $file,
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

        if (!empty($items)) {
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

    private function snakeCase(string $name): string
    {
        return u($name)->snake()->toString();
    }
}
