<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:05 PM
 */

namespace N3vrax\DkUser\Form;

use N3vrax\DkUser\Form\InputFilter\RegisterInputFilter;
use N3vrax\DkUser\Options\RegisterOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Form;

class RegisterForm extends Form
{
    use EventManagerAwareTrait;

    /** @var  RegisterOptions */
    protected $registerOptions;

    public function __construct(
        RegisterOptions $registerOptions,
        $name = 'register',
        $options = array())
    {
        $this->registerOptions = $registerOptions;
        parent::__construct($name, $options);
        $this->init();
    }

    public function init()
    {
        $this->add(array(
            'name' => 'email',
            'type' => 'text',
            'options' => [
                'label' => 'Email'
            ],
            'attributes' => array(
                'placeholder' => 'Email Address',
                //'required' => true,
                'autofocus' => true,
            ),

        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'username',
            'options' => [
                'label' => 'Username',
            ],
            'attributes' => array(
                'placeholder' => 'Username',
                //'required' => true,
            ),
        ));

        $this->add(array(
            'type' => 'password',
            'name' => 'password',
            'attributes' => array(
                'placeholder' => 'Password',
                //'required' => true,
            ),
        ), ['priority' => -20]);

        $this->add(array(
            'type' => 'password',
            'name' => 'passwordVerify',
            'attributes' => array(
                'placeholder' => 'Confirm Password',
                //'required' => true,
            ),
        ), ['priority' => -20]);

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Sign Up',
            ),
        ), ['priority' => -100]);

        $this->getEventManager()->trigger('init', $this);
    }
}