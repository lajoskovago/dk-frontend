<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/4/2016
 * Time: 11:37 PM
 */

namespace Frontend\User\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = 'user', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        
    }
}