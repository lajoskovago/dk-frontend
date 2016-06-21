<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/19/2016
 * Time: 7:06 PM
 */

namespace Frontend\User;

interface UserMapperInterface
{
    public function findUser($id);
    
    public function findUserBy($field, $id);
    
    public function findAllUsers(array $filters = []);
    
    public function saveUser($data);
    
    public function removeUser($id);
}