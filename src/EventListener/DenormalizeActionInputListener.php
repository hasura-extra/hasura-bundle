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

final class DenormalizeActionInputListener
{
    use RequestAttributeExtractionTrait;

    public function __construct(private SerializerInterface $serializer)
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

        if (null !== $inputClass) {
            $data['input'] = $this->serializer->denormalize(
                $data['input'],
                $inputClass,
                context: $descriptor->getAttribute('denormalizeContext') ?? []
            );

            $request->attributes->set('_hasura_request_data', $data);
        }
    }
}
