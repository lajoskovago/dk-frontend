<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 7:55 PM
 */

namespace N3vrax\DkUser\Entity;

class UserEntity
{
    /** @var  string|int */
    protected $id;

    /** @var  string */
    protected $username;

    /** @var  string */
    protected $email;

    /** @var  string */
    protected $password;

    /** @var  string|int */
    protected $dateCreated;

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     * @return UserEntity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return UserEntity
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return UserEntity
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return UserEntity
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param int|string $dateCreated
     * @return UserEntity
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

}