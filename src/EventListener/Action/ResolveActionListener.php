<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener\Action;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use VXM\Hasura\Action\Action;

final class ResolveActionListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $attributes = $event->getRequest()->attributes;
        /** @var Action $action */
        $action = $attributes->get('_hasura_action');
        $input = $attributes->get('_hasura_action_input');
        $sessionVariables = $attributes->get('_hasura_action_session_variables');

        if (null === $action || null === $input || null === $sessionVariables) {
            return;
        }

        $metadata = $action->getMetadata();
        $resolver = $action->getResolver();

        $result = $resolver->resolve($metadata->getName(), $input, $sessionVariables);
        $attributes->set('data', $result);
    }
}
