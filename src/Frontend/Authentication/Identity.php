<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/7/2016
 * Time: 10:09 PM
 */

namespace Frontend\Authentication;

use N3vrax\DkAuthentication\Identity\IdentityInterface as AuthenticationIdentityInterface;
use N3vrax\DkAuthorization\Identity\IdentityInterface as AuthorizationIdentityInterface;
use N3vrax\DkUser\Entity\UserEntity;

class Identity extends UserEntity implements AuthenticationIdentityInterface, AuthorizationIdentityInterface
{
    protected $role;

    public function getName()
    {
        if($this->username) {
            return $this->username;
        }

        return $this->email;
    }

    public function getRoles()
    {
        return [$this->role];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }


}