<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/14/2016
 * Time: 9:14 PM
 */

namespace Frontend\Event;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManagerInterface;

class EventManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new EventManager($container->has(SharedEventManagerInterface::class)
            ? $container->get(SharedEventManagerInterface::class) : null);
    }
}