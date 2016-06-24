<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:05 PM
 */

namespace N3vrax\DkUser\Form\InputFilter;

use N3vrax\DkUser\Options\RegisterOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\InputFilter\InputFilter;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Regex;

class RegisterInputFilter extends InputFilter
{
    use EventManagerAwareTrait;

    /** @var RegisterOptions  */
    protected $registerOptions;

    /** @var AbstractValidator */
    protected $emailValidator;

    /** @var AbstractValidator */
    protected $usernameValidator;

    public function __construct(RegisterOptions $options, $emailValidator = null, $usernameValidator = null)
    {
        $this->registerOptions = $options;
        $this->emailValidator = $emailValidator;
        $this->usernameValidator = $usernameValidator;
        $this->init();
    }

    public function init()
    {
        $email = [
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Email is required and cannot be empty'
                    ]
                ],
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'message' => 'Email address format is invalid'
                    ]
                ],
            ],
        ];

        if($this->emailValidator) {
            $this->emailValidator->setMessage('Email address is already registered');
            $email['validators'][] = $this->emailValidator;
        }

        $this->add($email);

        $username = [
            'name' => 'username',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim']
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Username is required and cannot be empty'
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 3,
                        'max' => 255,
                        'message' => 'Username must have at least 3 characters'
                    ]
                ],
                [
                    'name' => 'Regex',
                    'options' => [
                        'pattern' => '/^[a-zA-Z0-9-_]+$/',
                        'message' => 'Username must contain only alphanumeric characters'
                    ]
                ],
            ],
        ];

        if($this->usernameValidator) {
            $this->usernameValidator->setMessage('Username is already registered');
            $username['validators'][] = $this->usernameValidator;
        }

        $this->add($username);

        $this->add([
            'name'       => 'password',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Password is required and cannot be empty'
                    ]
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'message' => 'Password must have at least 4 characters'
                    ],
                ],
            ],
        ]);

        $this->add([
            'name'       => 'passwordVerify',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Password confirmation is required'
                    ]
                ],
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => 'password',
                        'message' => 'Password confirmation does not match'
                    ],
                ],
            ],
        ]);

        $this->getEventManager()->trigger('init', $this);
    }

}