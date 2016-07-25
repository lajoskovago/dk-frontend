<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/24/2016
 * Time: 7:46 PM
 */

namespace Frontend\User\Entity;

class UserEntityHydrator extends \N3vrax\DkUser\Entity\UserEntityHydrator
{
    public function  __construct($underscoreSeparatedKeys = false)
    {
        parent::__construct($underscoreSeparatedKeys);
        $this->addStrategy('details', new UserDetailsHydratorStrategy(new UserDetailsHydrator()));
    }
}