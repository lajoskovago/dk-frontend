<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:05 PM
 */

namespace N3vrax\DkUser\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Mapper\UserDbMapper;
use N3vrax\DkUser\Options\DbOptions;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Options\TableOptions;
use N3vrax\DkUser\Options\UserOptions;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\Feature\EventFeature;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class UserDbMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);
        $dbAdapter = $container->get($options->getDbOptions()->getDbAdapter());

        $resultSetPrototype = new HydratingResultSet(
            $container->get($options->getUserEntityHydrator()),
            $container->get($options->getUserEntityClass()));

        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $eventFeature = new EventFeature($eventManager);
        $mapper = new UserDbMapper(
            $options->getDbOptions()->getUserTable(),
            $options->getDbOptions(),
            $dbAdapter,
            $eventFeature,
            $resultSetPrototype);

        return $mapper;
    }

}