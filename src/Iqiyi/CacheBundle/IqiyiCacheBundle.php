<?php

/**
 * @author    Aaron Scherer <aequasi@gmail.com>
 * @date      2013
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 */

namespace Iqiyi\CacheBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Iqiyi\CacheBundle\DependencyInjection\Compiler;

/**
 * Class IqiyiCacheBundle
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class IqiyiCacheBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Compiler\SessionSupportCompilerPass());
        $container->addCompilerPass(new Compiler\DoctrineSupportCompilerPass());

        if ($container->getParameter('kernel.debug')) {
            $container->addCompilerPass(new Compiler\DataCollectorCompilerPass());
        }
    }
}
