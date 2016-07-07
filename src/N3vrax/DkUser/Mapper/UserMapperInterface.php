<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 7:55 PM
 */

namespace N3vrax\DkUser\Mapper;


interface UserMapperInterface extends MapperInterface
{
    public function findUser($id);

    public function findUserBy($field, $id);

    public function findAllUsers(array $filters = []);

    public function saveUser($data);

    public function removeUser($id);

    public function saveResetToken($data);

    public function saveConfirmToken($data);

    public function findResetToken($userId, $token);

    public function findConfirmToken($userId, $token);

    public function disableConfirmToken($userId, $token);
}