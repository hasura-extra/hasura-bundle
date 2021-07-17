<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\Fixture;

use VXM\Hasura\Attribute\AsActionHandler;
use VXM\Hasura\Handler\ActionHandlerInterface;

#[AsActionHandler(name: 'test')]
final class ActionHandler implements ActionHandlerInterface
{
    public function handle(string $action, object | array $input, array $sessionVariables): array | object
    {
        return ['ok'];
    }
}