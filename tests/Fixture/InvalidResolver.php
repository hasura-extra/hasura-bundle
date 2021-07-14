<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\Fixture;

use VXM\Hasura\Attribute\AsHasuraActionResolver;

#[AsHasuraActionResolver(actionName: "test")]
final class InvalidResolver
{

}