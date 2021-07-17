<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use VXM\Hasura\Handler\HandlerDescriptor;
use VXM\Hasura\Validation\ViolationHttpException;

final class ActionInputValidateListener
{
    use RequestAttributeExtractionTrait;

    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $attributes = $this->extractAttributes($event->getRequest(), 'action');

        if (null === $attributes) {
            return;
        }

        /** @var HandlerDescriptor $descriptor */
        [$descriptor, $data] = $attributes;

        if (!$descriptor->getAttribute('validate')) {
            return;
        }

        $violations = $this->validator->validate($data['input']);

        if (count($violations) > 0) {
            $violation = $violations->get(0);

            throw new ViolationHttpException($violation);
        }
    }
}
