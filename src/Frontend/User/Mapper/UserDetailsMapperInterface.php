<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/25/2016
 * Time: 3:14 AM
 */

namespace Frontend\User\Mapper;

interface UserDetailsMapperInterface
{
    public function getUserDetails($userId);

    public function insertUserDetails($data);

    public function updateUserDetails($userId, $data);
}