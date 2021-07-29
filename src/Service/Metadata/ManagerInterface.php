<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Service\Metadata;

interface ManagerInterface
{
    /**
     * @param bool $force export, delete all old metadata before export.
     */
    public function export(bool $force): void;

    /**
     * @param bool $allowInconsistency current metadata with database sources.
     */
    public function apply(bool $allowInconsistency = false): void;

    /**
     * @param bool $reloadRemoteSchemas whether reload remote schemas or not.
     * @param bool $reloadSources whether reload sources or not.
     */
    public function reload(bool $reloadRemoteSchemas = true, bool $reloadSources = true): void;

    /**
     * Clear metadata
     */
    public function clear(): void;

    /**
     * Return an array inconsistent metadata information.
     */
    public function getInconsistentMetadata(): array;

    /**
     * Drop inconsistent metadata.
     */
    public function dropInconsistentMetadata(): void;
}