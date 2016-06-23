<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 7:55 PM
 */

namespace N3vrax\DkUser\Mapper;

use N3vrax\DkUser\Entity\UserEntity;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class UserDbMapper extends TableGateway implements UserMapperInterface
{
    protected $idColumn = 'id';

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
        if($data instanceof UserEntity) {
            /** @var HydratingResultSet $resultSetPrototype */
            $resultSetPrototype = $this->resultSetPrototype;
            $hydrator = $resultSetPrototype->getHydrator();

            $data = $hydrator->extract($data);
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

}