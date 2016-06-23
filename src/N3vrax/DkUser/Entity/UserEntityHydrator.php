<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 7:56 PM
 */

namespace N3vrax\DkUser\Entity;

use Zend\Hydrator\ClassMethods;

class UserEntityHydrator extends ClassMethods
{
    public function __construct($underscoreSeparatedKeys = false)
    {
        parent::__construct($underscoreSeparatedKeys);
    }
}