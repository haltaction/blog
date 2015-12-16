<?php

namespace UserBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use UserBundle\DependencyInjection\RemoverPass;

class UserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RemoverPass());
    }

    public function getParent()
    {
        return 'FOSUserBundle';
    }
}