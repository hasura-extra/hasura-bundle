<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Validation;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use VXM\Hasura\Exception\HttpExceptionInterface;

final class ViolationHttpException extends BadRequestHttpException implements HttpExceptionInterface
{
    private string $violationCode;

    public function __construct(
        ?string $violationCode = '',
        ?string $message = '',
        \Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        $this->violationCode = (string)$violationCode;

        parent::__construct($message, $previous, $code, $headers);
    }

    public function getMessageCode(): string
    {
        return $this->getViolationCode();
    }

    public function getViolationCode(): string
    {
        return $this->violationCode;
    }
}