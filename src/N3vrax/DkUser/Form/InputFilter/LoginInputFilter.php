<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/21/2016
 * Time: 9:33 PM
 */

namespace N3vrax\DkUser\Form\InputFilter;

use N3vrax\DkUser\Options\LoginOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\InputFilter\InputFilter;

class LoginInputFilter extends InputFilter
{
    use EventManagerAwareTrait;

    /** @var  LoginOptions */
    protected $loginOptions;

    public function __construct(LoginOptions $options)
    {
        $this->loginOptions = $options;
        $this->init();
    }

    public function init()
    {
        $this->add([
            'name' => 'identity',
            'required' => true,
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Identity is required and cannot be empty'
                    ]
                ]
            ]
        ]);

        $this->add([
            'name' => 'password',
            'required' => true,
            'filters' => [],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Password is required and cannot be empty'
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'message' => 'Password must be at least 4 characters'
                    ]
                ]
            ],
        ]);

        $this->getEventManager()->trigger('init', $this);
    }
}