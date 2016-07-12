<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/21/2016
 * Time: 9:33 PM
 */

namespace N3vrax\DkUser\Form;

use N3vrax\DkUser\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Submit;
use Zend\Form\Form;

class LoginForm extends Form
{
    use EventManagerAwareTrait;

    /** @var  UserOptions */
    protected $userOptions;
    
    public function __construct(
        UserOptions $userOptions,
        $name = 'login',
        $options = array())
    {
        $this->userOptions = $userOptions;
        parent::__construct($name, $options);
        $this->init();
    }

    public function init()
    {
        $placeholder = '';
        foreach ($this->userOptions->getLoginOptions()->getAuthIdentityFields() as $field) {
            $placeholder = (!empty($placeholder) ? $placeholder . ' or ' : '') . ucfirst($field);
        }

        $this->add(array(
            'name' => 'identity',
            'type' => 'text',
            'options' => [
                'label' => $placeholder
            ],
            'attributes' => array(
                'placeholder' => $placeholder,
                //'required' => true,
                'autofocus' => true,
            ),

        ));
        $this->add(array(
            'type' => 'password',
            'name' => 'password',
            'options' => [
                'label' => 'Password'
            ],
            'attributes' => array(
                'placeholder' => 'Password',
                //'required' => true,
            ),
        ));

        if($this->userOptions->getLoginOptions()->isEnableRememberMe()) {
            $this->add(array(
                'type' => 'checkbox',
                'name' => 'remember',
                'options' => [
                    'label' => 'Remember Me',
                    'use_hidden_element' => true,
                    'checked_value' => 'yes',
                    'unchecked_value' => 'no',
                ],
                'attributes' => [
                    'value' => 'yes'
                ],
            ), ['priority' => -90]);
        }

        $csrf = new Csrf('login_csrf', [
            'csrf_options' => [
                'timeout' => $this->userOptions->getLoginOptions()->getLoginFormTimeout()
            ]
        ]);
        $this->add($csrf);

        $submitElement = new Submit('submit');
        $submitElement
            ->setLabel('Sign In')
            ->setValue('Sign In')
            ->setAttributes(array(
                'type'  => 'submit',
            ));
        $this->add($submitElement, array(
            'priority' => -100,
        ));
        
        $this->getEventManager()->trigger('init', $this);
    }
}