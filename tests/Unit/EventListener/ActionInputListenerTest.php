<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\Unit\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;
use VXM\Hasura\EventListener\ActionInputListener;
use VXM\Hasura\Tests\Fixture\ActionInput;
use VXM\Hasura\Validation\ViolationHttpException;

class ActionInputListenerTest extends TestCase
{
    use ResolvedRequestEventTrait;

    /**
     * @dataProvider requestDataProvider
     */
    public function testCanDenormalizeInput($requestData, bool $enabledValidate)
    {
        $event = $this->getRequestEvent(
            'action',
            [
                'inputClass' => ActionInput::class,
                'validate' => $enabledValidate
            ]
        );
        $request = $event->getRequest();
        $request->attributes->set('_hasura_request_data', $requestData);
        $listener = $this->getListener();
        $listener->onKernelRequest($event);

        $this->assertInstanceOf(ActionInput::class, $request->attributes->get('_hasura_request_data')['input']);
    }

    public function testValidateException()
    {
        $this->expectException(ViolationHttpException::class);

        $event = $this->getRequestEvent(
            'action',
            [
                'inputClass' => ActionInput::class,
                'validate' => true,
            ]
        );
        $request = $event->getRequest();
        $request->attributes->set('_hasura_request_data', ['input' => ['test' => null]]);
        $listener = $this->getListener();
        $listener->onKernelRequest($event);
    }

    private function getListener(): ActionInputListener
    {
        $serializer = new Serializer([new ObjectNormalizer()]);
        $validatorBuilder = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata');

        return new ActionInputListener($serializer, $validatorBuilder->getValidator());
    }

    public function requestDataProvider(): array
    {
        return [
            [['input' => ['test' => '']], false],
            [['input' => ['test' => 'test']], true]
        ];
    }
}