<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/19/2016
 * Time: 10:38 PM
 */

namespace Frontend\User;

use Interop\Container\ContainerInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\Feature\EventFeature;
use Zend\EventManager\EventManagerInterface;

class UserDbMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $dbAdapter = $container->get('database');
        $resultSetPrototype = new HydratingResultSet(new UserHydrator(false), new UserEntity());

        $eventManager = $container->get(EventManagerInterface::class);
        $eventFeature = new EventFeature($eventManager);

        $mapper = new UserDbMapper('user', $dbAdapter, $eventFeature, $resultSetPrototype);
        return $mapper;
    }
}