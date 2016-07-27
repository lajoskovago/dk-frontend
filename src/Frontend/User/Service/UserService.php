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
     * @param $field
     * @param $value
     * @return mixed
     */
    public function findUserBy($field, $value)
    {
        $user = parent::findUserBy($field, $value);
        if($user) {
            $details = $this->userDetailsMapper->getUserDetails($user->getId());
            if($details) {
                $user->setDetails($details);
            }
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
        //take the details object from user entity
        if($user instanceof UserEntity) {
            $details = $user->getDetails();
        }

        //save only the user entity to user table
        parent::saveUser($user);

        //store the generated user id, is it wasn't an update
        $userId = $user->getId();
        if(!$userId) {
            $userId = $this->userMapper->lastInsertValue();
        }

        //we make sure we insert details row even if empty data
        if(!$details) {
            $details = new UserDetailsEntity();
        }

        //decide if it is an insert or update
        if(!$user->getId()) {
            //make sure we have the user id in the object, only on insert
            $details->setUserId($userId);
            $user->setId($userId);

            $this->userDetailsMapper->insertUserDetails($details);
        }
        else {
            $this->userDetailsMapper->updateUserDetails($userId, $details);
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