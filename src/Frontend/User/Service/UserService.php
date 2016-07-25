<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/25/2016
 * Time: 3:09 AM
 */

namespace Frontend\User\Service;

use Frontend\User\Entity\UserDetailsEntity;
use Frontend\User\Entity\UserEntity;
use Frontend\User\Mapper\UserDetailsMapperInterface;
use N3vrax\DkUser\Entity\UserEntityInterface;

class UserService extends \N3vrax\DkUser\Service\UserService
{
    /** @var  UserDetailsMapperInterface */
    protected $userDetailsMapper;

    /**
     * @param $id
     * @return UserEntity
     */
    public function findUser($id)
    {
        /** @var UserEntity $user */
        $user = parent::findUser($id);
        if($user) {
            $details = $this->userDetailsMapper->getUserDetails($user->getId());
            $user->setDetails($details);
        }

        return $user;
    }

    /**
     * @param $field
     * @param $value
     * @return mixed
     */
    public function findUserBy($field, $value)
    {
        $user = parent::findUserBy($field, $value);
        if($user) {
            $details = $this->userDetailsMapper->getUserDetails($user->getId());
            $user->setDetails($details);
        }

        return $user;
    }

    /**
     * @param UserEntityInterface $user
     * @return void
     */
    public function saveUser(UserEntityInterface $user)
    {
        /** @var UserDetailsEntity $details */
        $details = null;
        if($user instanceof UserEntity) {
            $details = $user->getDetails();
            $user->setDetails(null);
        }
        parent::saveUser($user);

        $userId = $user->getId();
        if(!$userId) {
            $userId = $this->userMapper->lastInsertValue();
        }

        if($details) {
            if(!$user->getId()) {
                $details->setUserId($userId);
                $this->userDetailsMapper->insertUserDetails($details);
            }
            else {
                $this->userDetailsMapper->updateUserDetails($userId, $details);
            }
        }
    }

    /**
     * @return UserDetailsMapperInterface
     */
    public function getUserDetailsMapper()
    {
        return $this->userDetailsMapper;
    }

    /**
     * @param UserDetailsMapperInterface $userDetailsMapper
     * @return UserService
     */
    public function setUserDetailsMapper($userDetailsMapper)
    {
        $this->userDetailsMapper = $userDetailsMapper;
        return $this;
    }


}