<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/25/2016
 * Time: 3:16 AM
 */

namespace Frontend\User\Mapper;

use N3vrax\DkBase\Mapper\AbstractDbMapper;

class UserDetailsDbMapper extends AbstractDbMapper implements UserDetailsMapperInterface
{
    /** @var string  */
    protected $idColumn = 'userId';

    public function getUserDetails($userId)
    {
        return $this->select([$this->idColumn => $userId])->current();
    }

    public function insertUserDetails($data)
    {
        $data = $this->entityToArray($data);
        return $this->insert($data);
    }

    public function updateUserDetails($userId, $data)
    {
        $data = $this->entityToArray($data);
        return $this->update($data, [$this->idColumn => $userId]);
    }
}