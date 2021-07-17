<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener;

use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;
use VXM\Hasura\Handler\HandlerDescriptor;

final class ActionOutputListener
{
    use RequestAttributeExtractionTrait;

    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function onKernelView(ViewEvent $event): void
    {
        $attributes = $this->extractAttributes($event->getRequest(), 'action');

        if (null === $attributes) {
            return;
        }

        /** @var HandlerDescriptor $descriptor */
        [$descriptor,] = $attributes;
        $controllerResult = $event->getControllerResult();

        if (!is_array($controllerResult)) {
            $controllerResult = $this->serializer->normalize(
                $controllerResult,
                'json',
                $descriptor->getAttribute('normalizeContext') ?? []
            );

            $event->setControllerResult($controllerResult);
        }
    }
}
