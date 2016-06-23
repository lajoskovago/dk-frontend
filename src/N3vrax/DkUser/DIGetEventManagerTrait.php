<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/23/2016
 * Time: 8:39 PM
 */

namespace N3vrax\DkUser;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

trait DIGetEventManagerTrait
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