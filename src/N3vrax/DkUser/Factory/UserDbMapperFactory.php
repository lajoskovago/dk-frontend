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
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Options\TableOptions;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\Feature\EventFeature;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class UserDbMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var ModuleOptions $options */
        $options = $container->get(ModuleOptions::class);
        /** @var TableOptions $tableOptions */
        $tableOptions = $container->get(TableOptions::class);
        $dbAdapter = $container->get($options->getZendDbAdapter());

        $resultSetPrototype = new HydratingResultSet(
            $container->get($options->getUserEntityHydrator()),
            $container->get($options->getUserEntityClass()));

        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $eventFeature = new EventFeature($eventManager);
        $mapper = new UserDbMapper($tableOptions->getUserTable(), $dbAdapter, $eventFeature, $resultSetPrototype);
        $mapper->setUserResetTokenTable($tableOptions->getUserResetTokenTable());

        return $mapper;
    }

}