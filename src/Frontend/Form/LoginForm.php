<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/12/2016
 * Time: 1:45 AM
 */

namespace Frontend\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = 'login', $options = array())
    {
        parent::__construct($name, $options);
        $this->init();
    }

    public function init()
    {
        $this->add(array(
            'name' => 'identity',
            'type' => 'text',
            'options' => array(
                'placeholder' => 'Username or Email',
            ),

        ));
        $this->add(array(
            'type' => 'password',
            'name' => 'credential',
            'options' => array(
                'placeholder' => 'Password',
            ),
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Sign In',
            ),
        ));
    }
}