<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\ApiClient;

final class MetadataApi extends AbstractApi
{
    public function export(): array
    {
        $response = $this->request(
            [
                'type' => 'export_metadata',
                'version' => 2,
                'args' => []
            ]
        );

        return $response->toArray();
    }

    public function replace(array $metadata, bool $allowInconsistency = false): array
    {
        $response = $this->request(
            [
                'type' => 'replace_metadata',
                'version' => 2,
                'args' => [
                    'allow_inconsistent_metadata' => $allowInconsistency,
                    'metadata' => $metadata
                ]
            ]
        );

        return $response->toArray();
    }

    public function reload(bool $reloadRemoteSchemas = true, bool $reloadSources = true): array
    {
        $response = $this->request(
            [
                'type' => 'reload_metadata',
                'args' => [
                    'reload_remote_schemas' => $reloadRemoteSchemas,
                    'reload_sources' => $reloadSources
                ]
            ]
        );

        return $response->toArray();
    }

    public function clear(bool $reloadRemoteSchemas = true, bool $reloadSources = true): array
    {
        $response = $this->request(
            [
                'type' => 'clear_metadata',
                'args' => []
            ]
        );

        return $response->toArray();
    }

    public function getInconsistentMetadata(): array
    {
        $response = $this->request(
            [
                'type' => 'get_inconsistent_metadata',
                'args' => []
            ]
        );

        return $response->toArray();
    }

    public function dropInconsistentMetadata(): array
    {
        $response = $this->request(
            [
                'type' => 'drop_inconsistent_metadata',
                'args' => []
            ]
        );

        return $response->toArray();
    }


    protected function apiPath(): string
    {
        return 'metadata';
    }
}