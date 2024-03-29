<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface as BaseHttpExceptionInterface;

interface HttpExceptionInterface extends ExceptionInterface, BaseHttpExceptionInterface
{
    public function getMessageCode(): string;
}
