<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/9/2016
 * Time: 10:26 PM
 */

namespace N3vrax\DkUser;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

trait DiGetEventManagerTrait
{
    /**
     * @param ContainerInterface $container
     * @return mixed|EventManager
     */
    public function getEventManager(ContainerInterface $container)
    {
        $events = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();
        return $events;
    }
}