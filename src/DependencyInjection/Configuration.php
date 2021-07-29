<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $buidler = new TreeBuilder('hasura');
        $root = $buidler->getRootNode();
        $root
            ->children()
                ->scalarNode('base_uri')
                    ->cannotBeEmpty()
                    ->defaultValue('htpp://localhost:8080')
                ->end()
                ->scalarNode('admin_secret')
                    ->cannotBeEmpty()
                    ->defaultNull()
                ->end()
                ->scalarNode('metadata_path')
                    ->cannotBeEmpty()
                    ->defaultValue('%kernel.project_dir%/hasura')
                ->end()
            ->end();

        return $buidler;
    }
}
