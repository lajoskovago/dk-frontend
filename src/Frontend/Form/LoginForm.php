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
            'attributes' => array(
                'placeholder' => 'Username or Email...',
                //'required' => true,
                'autofocus' => true,
            ),

        ));
        $this->add(array(
            'type' => 'password',
            'name' => 'credential',
            'attributes' => array(
                'placeholder' => 'Password...',
                //'required' => true,
            ),
        ));
        $this->add(array(
            'type' => 'checkbox',
            'name' => 'remember',
            'options' => [
                'label' => 'Remember Me',
                'use_hidden_element' => false,
            ],
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