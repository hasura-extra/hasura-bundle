<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Validation;

use Symfony\Component\Validator\Constraints as Assert;

final class EventRequestValidator extends AbstractRequestValidator
{
    public const TYPE = 'event';

    protected function getConstraints(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\Collection(
                [
                    'id' => [
                        new Assert\NotBlank(),
                        new Assert\Uuid(),
                    ],
                    'created_at' => [
                        new Assert\NotBlank(),
                        new Assert\Type('string'),
                    ],
                    'trigger' => [
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
                    'table' => [
                        new Assert\NotBlank(),
                        new Assert\Collection(
                            [
                                'table' => [
                                    new Assert\NotBlank(),
                                    new Assert\Type('string'),
                                ],
                                'schema' => [
                                    new Assert\NotBlank(),
                                    new Assert\Type('string'),
                                ],
                            ]
                        ),
                    ],
                    'event' => [
                        new Assert\NotBlank(),
                        new Assert\Collection(
                            [
                                'op' => [
                                    new Assert\NotBlank(),
                                    new Assert\Choice(['INSERT', 'UPDATE', 'DELETE', 'MANUAL']),
                                ],
                                'data' => [
                                    new Assert\NotBlank(),
                                    new Assert\Collection(
                                        [
                                            'old' => [
                                                new Assert\NotBlank(allowNull: true),
                                                new Assert\Type('array'),
                                            ],
                                            'new' => [
                                                new Assert\NotBlank(allowNull: true),
                                                new Assert\Type('array'),
                                            ],
                                        ]
                                    ),
                                ],
                                'session_variables' => [
                                    new Assert\NotNull(),
                                    new Assert\Type('array'),
                                ],
                                'trace_context' => [
                                    new Assert\NotBlank(),
                                    new Assert\Collection(
                                        [
                                            'trace_id' => [
                                                new Assert\NotBlank(),
                                                new Assert\Type('string'),
                                            ],
                                            'span_id' => [
                                                new Assert\NotBlank(),
                                                new Assert\Type('string'),
                                            ],
                                        ]
                                    ),
                                ],
                            ]
                        ),
                    ],
                    'delivery_info' => [
                        new Assert\NotBlank(),
                        new Assert\Collection(
                            [
                                'max_retries' => [
                                    new Assert\NotBlank(),
                                    new Assert\Type('int'),
                                    new Assert\GreaterThanOrEqual(0),
                                ],
                                'current_retry' => [
                                    new Assert\NotBlank(),
                                    new Assert\Type('int'),
                                    new Assert\GreaterThanOrEqual(0),
                                ],
                            ]
                        ),
                    ],
                ]
            ),
        ];
    }
}
