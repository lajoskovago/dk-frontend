<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:05 PM
 */

namespace N3vrax\DkUser\Form\InputFilter;

use N3vrax\DkUser\DkUser;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\InputFilter\InputFilter;
use Zend\Validator\AbstractValidator;

class RegisterInputFilter extends InputFilter
{
    use EventManagerAwareTrait;

    /** @var  ModuleOptions */
    protected $options;

    /** @var RegisterOptions  */
    protected $registerOptions;

    /** @var AbstractValidator */
    protected $emailValidator;

    /** @var AbstractValidator */
    protected $usernameValidator;

    public function __construct(
        ModuleOptions $options,
        RegisterOptions $registerOptions,
        $emailValidator = null,
        $usernameValidator = null)
    {
        $this->options = $options;
        $this->registerOptions = $registerOptions;
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
                        'message' => $this->options->getMessage(DkUser::MESSAGE_REGISTER_EMPTY_EMAIL),
                    ]
                ],
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'message' => $this->options->getMessage(DkUser::MESSAGE_REGISTER_INVALID_EMAIL)
                    ]
                ],
            ],
        ];

        if($this->emailValidator) {
            $this->emailValidator->setMessage($this->options->getMessage(
                DkUser::MESSAGE_REGISTER_EMAIL_ALREADY_REGISTERED));

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
                        'message' => $this->options->getMessage(DkUser::MESSAGE_REGISTER_EMPTY_USERNAME)
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 3,
                        'max' => 255,
                        'message' => $this->options->getMessage(DkUser::MESSAGE_REGISTER_USERNAME_TOO_SHORT)
                    ]
                ],
                [
                    'name' => 'Regex',
                    'options' => [
                        'pattern' => '/^[a-zA-Z0-9-_]+$/',
                        'message' => $this->options->getMessage(DkUser::MESSAGE_REGISTER_USERNAME_INVALID_CHARACTERS)
                    ]
                ],
            ],
        ];

        if($this->usernameValidator) {
            $this->usernameValidator->setMessage($this->options->getMessage(
                DkUser::MESSAGE_REGISTER_USERNAME_ALREADY_REGISTERED));

            $username['validators'][] = $this->usernameValidator;
        }

        if($this->registerOptions->isEnableUsername()) {
            $this->add($username);
        }
        
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
                        'message' => $this->options->getMessage(DkUser::MESSAGE_REGISTER_EMPTY_PASSWORD)
                    ]
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'message' => $this->options->getMessage(DkUser::MESSAGE_REGISTER_PASSWORD_TOO_SHORT)
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
                        'message' => $this->options->getMessage(DkUser::MESSAGE_REGISTER_EMPTY_PASSWORD_CONFIRM)
                    ]
                ],
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => 'password',
                        'message' => $this->options->getMessage(DkUser::MESSAGE_REGISTER_PASSWORD_CONFIRM_NOT_MATCH)
                    ],
                ],
            ],
        ]);

        $this->getEventManager()->trigger('init', $this);
    }

}