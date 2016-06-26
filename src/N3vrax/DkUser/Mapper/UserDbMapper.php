<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 7:55 PM
 */

namespace N3vrax\DkUser\Mapper;

use N3vrax\DkUser\Entity\UserEntityInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class UserDbMapper extends TableGateway implements UserMapperInterface
{
    protected $idColumn = 'id';

    protected $userResetTokenTable = 'user_reset_token';

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

    public function saveResetPasswordToken($userId, $token, $expireAt)
    {
        //TODO: remove hardcoded table name
        $sql = new Sql($this->getAdapter(), $this->userResetTokenTable);
        $insert = $sql->insert();
        $insert->columns(['userId', 'token', 'expireAt'])->values([$userId, $token, $expireAt]);

        $stmt = $sql->prepareStatementForSqlObject($insert);
        return $stmt->execute();
    }

    public function findResetPasswordToken($userId)
    {
        $sql = new Sql($this->getAdapter(), $this->userResetTokenTable);
        $select = $sql->select()->where(['userId' => $userId]);

        $stmt = $sql->prepareStatementForSqlObject($select);
        return $stmt->execute()->current();
    }

    /**
     * @return string
     */
    public function getUserResetTokenTable()
    {
        return $this->userResetTokenTable;
    }

    /**
     * @param string $userResetTokenTable
     * @return UserDbMapper
     */
    public function setUserResetTokenTable($userResetTokenTable)
    {
        $this->userResetTokenTable = $userResetTokenTable;
        return $this;
    }



}