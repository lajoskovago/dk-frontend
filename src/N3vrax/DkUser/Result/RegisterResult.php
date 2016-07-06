<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/7/2016
 * Time: 12:51 AM
 */

namespace N3vrax\DkUser\Result;

use N3vrax\DkUser\Entity\UserEntityInterface;

class RegisterResult extends AbstractResult
{
    /** @var  UserEntityInterface */
    protected $user;

    /**
     * @return UserEntityInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserEntityInterface $user
     * @return RegisterResult
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    
}