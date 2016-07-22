<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/23/2016
 * Time: 1:29 AM
 */

namespace Frontend\User\Entity;

class UserEntity extends \N3vrax\DkUser\Entity\UserEntity
{
    /** @var  UserDetailsEntity */
    protected $details;

    /**
     * @return UserDetailsEntity
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param UserDetailsEntity $details
     * @return UserEntity
     */
    public function setDetails($details)
    {
        $this->details = $details;
        return $this;
    }


}