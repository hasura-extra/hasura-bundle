<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\Fixture;

use Symfony\Component\HttpFoundation\Request;

trait HandleRequestTestTrait
{
    private Request $requestTest;

    public function setRequestTest(Request $request)
    {
        $this->requestTest = $request;
    }
}