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

    protected $userConfirmTokenTable = 'user_confirm_token';

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
        $sql = new Sql($this->getAdapter(), $this->userResetTokenTable);
        $insert = $sql->insert();
        $insert->columns(array_keys($data))->values($data);

        $stmt = $sql->prepareStatementForSqlObject($insert);
        return $stmt->execute();
    }

    public function findResetToken($userId, $token)
    {
        $sql = new Sql($this->getAdapter(), $this->userResetTokenTable);
        $select = $sql->select()->where(['userId' => $userId, 'token' => $token]);

        $stmt = $sql->prepareStatementForSqlObject($select);
        return $stmt->execute()->current();
    }

    public function saveConfirmToken($data)
    {
        $sql = new Sql($this->getAdapter(), $this->userConfirmTokenTable);
        $insert = $sql->insert();
        $insert->columns(array_keys($data))->values($data);

        $stmt = $sql->prepareStatementForSqlObject($insert);
        return $stmt->execute();
    }

    public function findConfirmToken($userId, $token)
    {
        $sql = new Sql($this->getAdapter(), $this->userConfirmTokenTable);
        $select = $sql->select()->where(['userId' => $userId, 'token' => $token]);

        $stmt = $sql->prepareStatementForSqlObject($select);
        return $stmt->execute()->current();
    }

    /**
     * @return string
     */
    public function getUserConfirmTokenTable()
    {
        return $this->userConfirmTokenTable;
    }

    /**
     * @param string $userConfirmTokenTable
     * @return UserDbMapper
     */
    public function setUserConfirmTokenTable($userConfirmTokenTable)
    {
        $this->userConfirmTokenTable = $userConfirmTokenTable;
        return $this;
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