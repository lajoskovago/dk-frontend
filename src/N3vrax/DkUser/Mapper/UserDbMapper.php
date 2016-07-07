<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 7:55 PM
 */

namespace N3vrax\DkUser\Mapper;

use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Options\DbOptions;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class UserDbMapper extends TableGateway implements UserMapperInterface
{
    protected $idColumn = 'id';

    /** @var  DbOptions */
    protected $dbOptions;

    public function __construct(
        $table,
        DbOptions $dbOptions,
        AdapterInterface $adapter,
        $features = null,
        $resultSetPrototype = null,
        $sql = null)
    {
        parent::__construct($table, $adapter, $features, $resultSetPrototype, $sql);
    }

    public function findUser($id)
    {
        return $this->findUserBy($this->idColumn, $id);
    }

    public function findUserBy($field, $value)
    {
        $result = $this->select([$field => $value]);
        return $result->current();
    }

    public function findAllUsers(array $filters = [])
    {
        return $this->select();
    }

    public function saveUser($data)
    {
        if($data instanceof UserEntityInterface) {
            /** @var HydratingResultSet $resultSetPrototype */
            $resultSetPrototype = $this->resultSetPrototype;
            $hydrator = $resultSetPrototype->getHydrator();

            $data = $hydrator->extract($data);
            //remove empty|null keys
            $data = array_filter($data);
        }

        if(isset($data[$this->idColumn])) {
            $id = $data[$this->idColumn];
            unset($data[$this->idColumn]);

            return $this->update($data, [$this->idColumn => $id]);
        }
        else {
            return $this->insert($data);
        }
    }

    public function removeUser($id)
    {
        return $this->delete([$this->idColumn => $id]);
    }

    public function lastInsertValue()
    {
        return $this->getLastInsertValue();
    }

    public function saveResetToken($data)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserResetTokenTable());
        $insert = $sql->insert();
        $insert->columns(array_keys($data))->values($data);

        $stmt = $sql->prepareStatementForSqlObject($insert);
        return $stmt->execute();
    }

    public function findResetToken($userId, $token)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserResetTokenTable());
        $select = $sql->select()->where(['userId' => $userId, 'token' => $token]);

        $stmt = $sql->prepareStatementForSqlObject($select);
        return $stmt->execute()->current();
    }

    public function saveConfirmToken($data)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserConfirmTokenTable());
        $insert = $sql->insert();
        $insert->columns(array_keys($data))->values($data);

        $stmt = $sql->prepareStatementForSqlObject($insert);
        return $stmt->execute();
    }

    public function findConfirmToken($userId, $token)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserConfirmTokenTable());
        $select = $sql->select()->where(['userId' => $userId, 'token' => $token]);

        $stmt = $sql->prepareStatementForSqlObject($select);
        return $stmt->execute()->current();
    }
}