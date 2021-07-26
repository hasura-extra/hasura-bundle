<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Fixture;

use Hasura\Attribute\AsActionHandler;
use Hasura\Handler\ActionHandlerInterface;

#[AsActionHandler(name: 'testWithoutInputClass')]
final class ActionHandlerWithoutInputClass implements ActionHandlerInterface
{
    public function handle(string $action, object | array $input, array $sessionVariables): array | object
    {
        return $input;
    }
}
