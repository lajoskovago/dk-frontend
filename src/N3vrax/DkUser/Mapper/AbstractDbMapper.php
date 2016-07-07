<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/7/2016
 * Time: 9:06 PM
 */

namespace N3vrax\DkUser\Mapper;

use Zend\Db\TableGateway\TableGateway;

abstract class AbstractDbMapper extends TableGateway implements MapperInterface
{
    public function beginTransaction()
    {
        $connection = $this->getAdapter()->getDriver()->getConnection();
        $connection->beginTransaction();
    }

    public function commit()
    {
        $connection = $this->getAdapter()->getDriver()->getConnection();
        $connection->commit();
    }

    public function rollback()
    {
        $connection = $this->getAdapter()->getDriver()->getConnection();
        $connection->rollback();
    }

    public function lastInsertValue()
    {
        return $this->getLastInsertValue();
    }
}