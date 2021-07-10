<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use VXM\Hasura\DependencyInjection\Compiler\ActionPass;
use VXM\Hasura\DependencyInjection\Compiler\EventTriggerPass;

final class HasuraBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ActionPass());
        $container->addCompilerPass(new EventTriggerPass());
    }
}