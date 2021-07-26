<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Validation;

use Symfony\Component\Validator\Constraints as Assert;

final class ActionRequestValidator extends AbstractRequestValidator
{
    protected const TYPE = 'action';

    protected function getConstraints(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\Collection(
                [
                    'action' => [
                        new Assert\NotBlank(),
                        new Assert\Collection(
                            [
                                'name' => [
                                    new Assert\NotBlank(),
                                    new Assert\Type('string'),
                                ],
                            ]
                        ),
                    ],
                    'session_variables' => [
                        new Assert\NotNull(),
                        new Assert\Type('array'),
                    ],
                    'input' => [
                        new Assert\NotNull(),
                        new Assert\Type('array'),
                    ],
                ]
            ),
        ];
    }
}
