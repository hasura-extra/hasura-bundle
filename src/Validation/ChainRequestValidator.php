<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Validation;

use Symfony\Component\HttpFoundation\Request;

final class ChainRequestValidator implements RequestValidatorInterface
{
    public function __construct(private iterable $requestValidators)
    {
    }

    public function validate(Request $request): void
    {
        foreach ($this->requestValidators as $validator) {
            $validator->validate($request);
        }
    }
}
