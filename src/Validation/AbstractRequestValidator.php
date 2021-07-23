<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Validation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractRequestValidator implements RequestValidatorInterface
{
    protected const TYPE = '';

    public function __construct(private ValidatorInterface $validator)
    {
    }

    final public function validate(Request $request): void
    {
        $attributes = $request->attributes;
        $type = $attributes->get('_hasura');

        if (static::TYPE !== $type) {
            return;
        }

        if ('json' !== $request->getContentType()) {
            throw new UnsupportedMediaTypeHttpException('Only support `json` MIME type.');
        }

        $content = json_decode($request->getContent(), true);

        try {
            $violations = $this->validator->validate($content, $this->getConstraints());

            if (count($violations) > 0) {
                throw new \UnexpectedValueException($violations->get(0)->getMessage());
            }
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Bad request content!', $e, $e->getCode());
        }
    }

    abstract protected function getConstraints(): array;
}
