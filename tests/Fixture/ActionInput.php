<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Fixture;

use Symfony\Component\Validator\Constraints as Assert;

final class ActionInput
{
    #[Assert\NotBlank()]
    public $test;
}
