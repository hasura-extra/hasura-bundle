<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Service\Metadata;

final class FileExport
{
    public const SOURCES = 'sources.yaml';

    public const ALLOW_LIST = 'allow_list.yaml';

    public const REST_ENDPOINTS = 'rest_endpoints.yaml';

    public const QUERY_COLLECTION = 'query_collections.yaml';

    public const REMOTE_SCHEMAS = 'remote_schemas.yaml';

    public const VERSION = 'version.yaml';

    public const INHERITED_ROLES = 'inherited_roles.yaml';

    public const ACTIONS = 'actions.yaml';

    public const CRON_TRIGGERS = 'cron_triggers.yaml';

    public const CUSTOM_TYPES = 'custom_types.yaml';

    public const FIELDS_MAPPING = [
        'version' => self::VERSION,
        'sources' => self::SOURCES,
        'allowlist' => self::ALLOW_LIST,
        'remote_schemas' => self::REMOTE_SCHEMAS,
        'actions' => self::ACTIONS,
        'cron_triggers' => self::CRON_TRIGGERS,
        'inherited_roles' => self::INHERITED_ROLES,
        'query_collections' => self::QUERY_COLLECTION,
        'rest_endpoints' => self::REST_ENDPOINTS,
        'custom_types' => self::CUSTOM_TYPES
    ];
}