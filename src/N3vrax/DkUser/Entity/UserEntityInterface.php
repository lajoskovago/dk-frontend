<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/24/2016
 * Time: 3:27 PM
 */
namespace N3vrax\DkUser\Entity;

interface UserEntityInterface
{
    /**
     * @return int|string
     */
    public function getId();

    /**
     * @param int|string $id
     * @return UserEntity
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param string $username
     * @return UserEntity
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     * @return UserEntity
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param string $password
     * @return UserEntity
     */
    public function setPassword($password);
}