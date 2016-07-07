<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/21/2016
 * Time: 9:33 PM
 */

namespace N3vrax\DkUser\Form\InputFilter;

use N3vrax\DkUser\Options\LoginOptions;
use N3vrax\DkUser\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\InputFilter\InputFilter;

class LoginInputFilter extends InputFilter
{
    use EventManagerAwareTrait;

    /** @var  UserOptions */
    protected $options;

    public function __construct(UserOptions $options)
    {
        $this->options = $options;
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
                        'message' => $this->options->getLoginOptions()
                            ->getMessage(LoginOptions::MESSAGE_LOGIN_EMPTY_IDENTITY)
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
                        'message' => $this->options->getLoginOptions()
                            ->getMessage(LoginOptions::MESSAGE_LOGIN_EMPTY_PASSWORD)
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'message' => $this->options->getLoginOptions()
                            ->getMessage(LoginOptions::MESSAGE_LOGIN_PASSWORD_TOO_SHORT)
                    ]
                ]
            ],
        ]);

        $this->getEventManager()->trigger('init', $this);
    }
}