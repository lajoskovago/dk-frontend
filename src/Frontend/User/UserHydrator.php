<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/19/2016
 * Time: 7:22 PM
 */

namespace Frontend\User;

use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Filter\MethodMatchFilter;

class UserHydrator extends ClassMethods
{
    public function __construct($underscoreSeparatedKeys)
    {
        parent::__construct(false);
        $this->addFilter('getName', new MethodMatchFilter('getName'), FilterComposite::CONDITION_AND);
        $this->addFilter('getRoles', new MethodMatchFilter('getRoles'), FilterComposite::CONDITION_AND);
    }
}