<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/19/2016
 * Time: 10:54 PM
 */

namespace Frontend\Controller\Factory;

use Frontend\Controller\PageController;
use Interop\Container\ContainerInterface;

class PageControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new PageController();
    }
}