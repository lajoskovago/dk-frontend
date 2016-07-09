<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/6/2016
 * Time: 8:13 PM
 */

namespace N3vrax\DkUser\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

class PasswordRecoveryOptions extends AbstractOptions
{
    const MESSAGE_RESET_PASSWORD_INVALID_EMAIL = 1;
    const MESSAGE_RESET_PASSWORD_INVALID_TOKEN = 2;
    const MESSAGE_RESET_PASSWORD_TOKEN_EXPIRED = 3;
    const MESSAGE_RESET_PASSWORD_MISSING_PARAMS = 4;
    const MESSAGE_RESET_PASSWORD_EMPTY_PASSWORD = 5;
    const MESSAGE_RESET_PASSWORD_TOO_SHORT = 6;
    const MESSAGE_RESET_PASSWORD_EMPTY_VERIFY = 7;
    const MESSAGE_RESET_PASSWORD_MISMATCH = 8;
    const MESSAGE_RESET_PASSWORD_DISABLED = 9;
    const MESSAGE_RESET_PASSWORD_ERROR = 10;
    const MESSAGE_RESET_PASSWORD_SUCCESS = 11;

    const MESSAGE_FORGOT_PASSWORD_MISSING_EMAIL = 12;
    const MESSAGE_FORGOT_PASSWORD_ERROR = 13;
    const MESSAGE_FORGOT_PASSWORD_SUCCESS = 14;

    /** @var bool  */
    protected $enablePasswordRecovery = true;

    /** @var int  */
    protected $resetPasswordTokenTimeout = 1800;

    /** @var array  */
    protected $messages = [
        PasswordRecoveryOptions::MESSAGE_FORGOT_PASSWORD_ERROR => 'Password reset request error. Please try again',
        PasswordRecoveryOptions::MESSAGE_FORGOT_PASSWORD_MISSING_EMAIL => 'Email address is required and cannot be empty',
        PasswordRecoveryOptions::MESSAGE_FORGOT_PASSWORD_SUCCESS => [
            'Password reset request successfully registered',
            'You\'ll receive in email with further instructions'
        ],

        PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_DISABLED => 'Password recovery is disabled',
        PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_ERROR => 'Password reset error. Please try again',
        PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_INVALID_EMAIL => 'Password reset error. Invalid parameters',
        PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_INVALID_TOKEN => 'Password reset error. Invalid parameters',
        PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_MISSING_PARAMS => 'Password reset error. Invalid parameters',
        PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_EMPTY_PASSWORD => 'Password is required and cannot be empty',
        PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_TOO_SHORT => 'Password must have at least 4 characters',
        PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_EMPTY_VERIFY => 'Password confirmation is required and cannot be empty',
        PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_MISMATCH => 'Password confirmation does not match',
        PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_TOKEN_EXPIRED => 'Password reset error. Reset token has expired',
        PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_SUCCESS => 'Account password successfully updated',
    ];

    /**
     * @return boolean
     */
    public function isEnablePasswordRecovery()
    {
        return $this->enablePasswordRecovery;
    }

    /**
     * @param boolean $enablePasswordRecovery
     * @return PasswordRecoveryOptions
     */
    public function setEnablePasswordRecovery($enablePasswordRecovery)
    {
        $this->enablePasswordRecovery = $enablePasswordRecovery;
        return $this;
    }

    /**
     * @return int
     */
    public function getResetPasswordTokenTimeout()
    {
        return $this->resetPasswordTokenTimeout;
    }

    /**
     * @param int $resetPasswordTokenTimeout
     * @return PasswordRecoveryOptions
     */
    public function setResetPasswordTokenTimeout($resetPasswordTokenTimeout)
    {
        $this->resetPasswordTokenTimeout = $resetPasswordTokenTimeout;
        return $this;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     * @return PasswordRecoveryOptions
     */
    public function setMessages($messages)
    {
        $this->messages = ArrayUtils::merge($this->messages, $messages, true);
        return $this;
    }

    /**
     * @param $key
     * @return mixed|string
     */
    public function getMessage($key)
    {
        return isset($this->messages[$key]) ? $this->messages[$key] : 'Unknown message';
    }

}