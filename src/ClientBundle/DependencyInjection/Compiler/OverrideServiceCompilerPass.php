<?php
/**
 * Created by PhpStorm.
 * User: khoa.nguyen
 * Date: 8/10/2015
 * Time: 3:13 PM
 */

namespace ClientBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface {

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        /* User Service */
        $userDefinition = $container->getDefinition('user');
        $userDefinition->setClass('ClientBundle\Service\UserService');

        /* Public Service */
        $publicDefinition = $container->getDefinition('public');
        $publicDefinition->setClass('ClientBundle\Service\PublicService');
    }
}