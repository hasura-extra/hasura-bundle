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
use VXM\Hasura\EventListener\ActionOutputListener;
use VXM\Hasura\Tests\Fixture\ActionOutput;

class ActionOutputListenerTest extends TestCase
{
    use ResolvedRequestEventTrait;

    /**
     * @dataProvider controllerResultDataProvider
     */
    public function testCanNormalizeOutput(mixed $output, mixed $normalizedOutput)
    {
        $event = $this->getViewEvent('action', $output);
        $listener = $this->getListener();
        $listener->onKernelView($event);
        $this->assertSame($normalizedOutput, $event->getControllerResult());
    }

    private function getListener(): ActionOutputListener
    {
        $serializer = new Serializer([new ObjectNormalizer()]);

        return new ActionOutputListener($serializer);
    }

    public function controllerResultDataProvider()
    {
        return [
            [['test' => 'test'], ['test' => 'test']],
            [new ActionOutput(), ['test' => 'test']],
            [null, null],
        ];
    }
}