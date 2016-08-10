<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/6/2016
 * Time: 12:06 AM
 */

namespace Frontend\User\Service;

use N3vrax\DkUser\Entity\UserEntityInterface;

interface UserServiceInterface extends \N3vrax\DkUser\Service\UserServiceInterface
{
    public function updateAccountInfo(UserEntityInterface $user);
}