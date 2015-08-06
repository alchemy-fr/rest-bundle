<?php

namespace Alchemy\RestBundle;

use Alchemy\RestBundle\DependencyInjection\Compiler\TransformerCompilerPass;
use Alchemy\RestBundle\DependencyInjection\RestExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RestBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TransformerCompilerPass());
    }

    public function getContainerExtension()
    {
        return new RestExtension();
    }
}
