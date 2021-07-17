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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use VXM\Hasura\Validation\ViolationHttpException;

final class ActionInputListener
{
    use RequestAttributeExtractionTrait;

    public function __construct(private SerializerInterface $serializer, private ValidatorInterface $validator)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $attributes = $this->extractAttributes($request, 'action');

        if (null === $attributes) {
            return;
        }

        [$descriptor, $data] = $attributes;

        $inputClass = $descriptor->getAttribute('inputClass');
        $validate = $descriptor->getAttribute('validate');

        if (null !== $inputClass) {
            $input = $data['input'] = $this->serializer->denormalize(
                $data['input'],
                $inputClass,
                context: $descriptor->getAttribute('denormalizeContext') ?? []
            );

            $request->attributes->set('_hasura_request_data', $data);

            if (!$validate) {
                return;
            }

            $violations = $this->validator->validate($input);

            if (count($violations) > 0) {
                $violation = $violations->get(0);

                throw new ViolationHttpException($violation);
            }
        }
    }
}
