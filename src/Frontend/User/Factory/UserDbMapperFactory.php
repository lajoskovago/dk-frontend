<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/2/2016
 * Time: 7:56 PM
 */

namespace Frontend\User\Factory;

use Frontend\User\Mapper\UserDbMapper;
use Frontend\User\Mapper\UserDetailsMapperInterface;
use Interop\Container\ContainerInterface;
use N3vrax\DkUser\Options\UserOptions;
use Zend\Db\ResultSet\HydratingResultSet;

class UserDbMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);
        $dbAdapter = $container->get($options->getDbOptions()->getDbAdapter());

        $resultSetPrototype = new HydratingResultSet(
            $container->get($options->getUserEntityHydrator()),
            $container->get($options->getUserEntity()));

        $mapper = new UserDbMapper(
            $options->getDbOptions()->getUserTable(),
            $options->getDbOptions(),
            $dbAdapter,
            null,
            $resultSetPrototype);
        
        $mapper->setUserDetailsMapper($container->get(UserDetailsMapperInterface::class));

        return $mapper;
    }
}