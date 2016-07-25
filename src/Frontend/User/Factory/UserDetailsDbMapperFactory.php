<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/25/2016
 * Time: 8:24 PM
 */

namespace Frontend\User\Factory;

use Frontend\User\Entity\UserDetailsEntity;
use Frontend\User\Entity\UserDetailsHydrator;
use Frontend\User\Mapper\UserDetailsDbMapper;
use Interop\Container\ContainerInterface;
use Zend\Db\ResultSet\HydratingResultSet;

class UserDetailsDbMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $dbAdapter = $container->get('database');
        $resultSet = new HydratingResultSet(
            new UserDetailsHydrator(),
            new UserDetailsEntity()
        );

        $mapper = new UserDetailsDbMapper('user_details', $dbAdapter, null, $resultSet);
        return $mapper;
    }
}