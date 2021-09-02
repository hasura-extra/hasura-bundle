<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Service;

use GraphQL\Type\Introspection;
use GraphQL\Type\Schema;
use GraphQL\Utils\BuildClientSchema;
use Hasura\ApiClient\Client;

final class SchemaFactory
{
    public function __construct(private Client $client)
    {
    }

    public function fromIntrospection(): Schema
    {
        $query = Introspection::getIntrospectionQuery();
        $responseData = $this->client->graphql()->query($query);

        return BuildClientSchema::build($responseData['data']);
    }
}
