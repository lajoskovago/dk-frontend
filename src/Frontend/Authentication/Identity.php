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

class Identity implements AuthenticationIdentityInterface, AuthorizationIdentityInterface
{
    protected $id;

    protected $username;

    protected $email;

    protected $role;

    protected $dateCreated;

    public function getName()
    {
        if($this->username) {
            return $this->username;
        }

        return $this->email;
    }

    public function getRoles()
    {
        return $this->role;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
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