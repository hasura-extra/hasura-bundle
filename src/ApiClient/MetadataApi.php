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