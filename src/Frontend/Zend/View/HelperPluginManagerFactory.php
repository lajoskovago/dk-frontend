<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/12/2016
 * Time: 2:36 PM
 */

namespace Frontend\Zend\View;

use Interop\Container\ContainerInterface;
use Zend\View\HelperPluginManager;

class HelperPluginManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')['view_helpers'];
        return new HelperPluginManager($container, $config);
    }
}