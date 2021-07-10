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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use VXM\Hasura\Action\Action;
use VXM\Hasura\Validation\ActionInputValidationException;

class ActionInputValidateListener
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        /** @var Action $action */
        $action = $request->attributes->get('_hasura_action');

        if (null === $action) {
            return;
        }

        $metadata = $action->getMetadata();

        if (!$metadata->shouldValidate()) {
            return;
        }

        $input = $request->attributes->get('_hasura_action_input');
        $violations = $this->validator->validate($input);

        if (count($violations) > 0) {
            $violation = $violations->get(0);

            throw new ActionInputValidationException($violation->getMessage(), $violation->getCode());
        }
    }
}