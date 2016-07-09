<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/26/2016
 * Time: 8:54 PM
 */

namespace N3vrax\DkUser\Form\InputFilter;

use N3vrax\DkUser\Options\PasswordRecoveryOptions;
use N3vrax\DkUser\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\InputFilter\InputFilter;

class ResetPasswordInputFilter extends InputFilter
{
    use EventManagerAwareTrait;

    /** @var  UserOptions */
    protected $options;

    public function __construct(
        UserOptions $options
    )
    {
        $this->options = $options;
        $this->init();
    }

    public function init()
    {
        $this->add([
            'name'       => 'newPassword',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => $this->options->getPasswordRecoveryOptions()
                            ->getMessage(PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_EMPTY_PASSWORD)
                    ]
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'message' => $this->options->getPasswordRecoveryOptions()
                            ->getMessage(PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_TOO_SHORT)
                    ],
                ],
            ],
        ]);

        $this->add([
            'name'       => 'newPasswordVerify',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => $this->options->getPasswordRecoveryOptions()
                            ->getMessage(PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_EMPTY_VERIFY)
                    ]
                ],
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => 'newPassword',
                        'message' => $this->options->getPasswordRecoveryOptions()
                            ->getMessage(PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_MISMATCH)
                    ],
                ],
            ],
        ]);

        $this->getEventManager()->trigger('init', $this);
    }
}