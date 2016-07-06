<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/23/2016
 * Time: 7:48 PM
 */

namespace N3vrax\DkUser\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

class RegisterOptions extends AbstractOptions
{
    const MESSAGE_REGISTER_EMPTY_EMAIL = 1;
    const MESSAGE_REGISTER_INVALID_EMAIL = 2;
    const MESSAGE_REGISTER_EMAIL_ALREADY_REGISTERED = 3;
    const MESSAGE_REGISTER_EMPTY_USERNAME = 4;
    const MESSAGE_REGISTER_USERNAME_TOO_SHORT = 5;
    const MESSAGE_REGISTER_USERNAME_INVALID_CHARACTERS = 6;
    const MESSAGE_REGISTER_USERNAME_ALREADY_REGISTERED = 7;
    const MESSAGE_REGISTER_EMPTY_PASSWORD = 8;
    const MESSAGE_REGISTER_PASSWORD_TOO_SHORT = 9;
    const MESSAGE_REGISTER_EMPTY_PASSWORD_CONFIRM = 10;
    const MESSAGE_REGISTER_PASSWORD_CONFIRM_NOT_MATCH = 11;
    const MESSAGE_REGISTER_ERROR = 12;
    const MESSAGE_REGISTER_SUCCESS = 13;

    /** @var bool  */
    protected $enableRegistration = true;

    /** @var bool  */
    protected $enableUsername = true;

    /** @var  mixed */
    protected $defaultUserStatus;

    /** @var int  */
    protected $userFormTimeout = 1800;

    /** @var bool  */
    protected $useRegistrationFormCaptcha = true;

    /** @var  mixed */
    protected $formCaptchaOptions;

    /** @var bool  */
    protected $loginAfterRegistration = false;

    /** @var array  */
    protected $messages = [
        RegisterOptions::MESSAGE_REGISTER_EMPTY_EMAIL => 'Email address is required and cannot be empty',
        RegisterOptions::MESSAGE_REGISTER_INVALID_EMAIL => 'Email address format is not valid',
        RegisterOptions::MESSAGE_REGISTER_EMAIL_ALREADY_REGISTERED => 'Email address is already in use',
        RegisterOptions::MESSAGE_REGISTER_EMPTY_USERNAME => 'Username is required and cannot be empty',
        RegisterOptions::MESSAGE_REGISTER_USERNAME_TOO_SHORT => 'Username must have at least 4 characters',
        RegisterOptions::MESSAGE_REGISTER_USERNAME_INVALID_CHARACTERS => 'Username contains invalid characters',
        RegisterOptions::MESSAGE_REGISTER_USERNAME_ALREADY_REGISTERED => 'Username is already in use',
        RegisterOptions::MESSAGE_REGISTER_EMPTY_PASSWORD => 'Password is required and cannot be empty',
        RegisterOptions::MESSAGE_REGISTER_PASSWORD_TOO_SHORT => 'Password must have at least 4 characters',
        RegisterOptions::MESSAGE_REGISTER_EMPTY_PASSWORD_CONFIRM => 'Password confirmation is required',
        RegisterOptions::MESSAGE_REGISTER_PASSWORD_CONFIRM_NOT_MATCH => 'The two passwords do not match',
        RegisterOptions::MESSAGE_REGISTER_ERROR => 'Registration error. Please try again',
        RegisterOptions::MESSAGE_REGISTER_SUCCESS => 'Account successfully created',
    ];

    /**
     * @return boolean
     */
    public function isEnableRegistration()
    {
        return $this->enableRegistration;
    }

    /**
     * @param boolean $enableRegistration
     * @return RegisterOptions
     */
    public function setEnableRegistration($enableRegistration)
    {
        $this->enableRegistration = $enableRegistration;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnableUsername()
    {
        return $this->enableUsername;
    }

    /**
     * @param boolean $enableUsername
     * @return RegisterOptions
     */
    public function setEnableUsername($enableUsername)
    {
        $this->enableUsername = $enableUsername;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultUserStatus()
    {
        return $this->defaultUserStatus;
    }

    /**
     * @param mixed $defaultUserStatus
     * @return RegisterOptions
     */
    public function setDefaultUserStatus($defaultUserStatus)
    {
        $this->defaultUserStatus = $defaultUserStatus;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserFormTimeout()
    {
        return $this->userFormTimeout;
    }

    /**
     * @param int $userFormTimeout
     * @return RegisterOptions
     */
    public function setUserFormTimeout($userFormTimeout)
    {
        $this->userFormTimeout = $userFormTimeout;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isUseRegistrationFormCaptcha()
    {
        return $this->useRegistrationFormCaptcha;
    }

    /**
     * @param boolean $useRegistrationFormCaptcha
     * @return RegisterOptions
     */
    public function setUseRegistrationFormCaptcha($useRegistrationFormCaptcha)
    {
        $this->useRegistrationFormCaptcha = $useRegistrationFormCaptcha;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormCaptchaOptions()
    {
        return $this->formCaptchaOptions;
    }

    /**
     * @param mixed $formCaptchaOptions
     * @return RegisterOptions
     */
    public function setFormCaptchaOptions($formCaptchaOptions)
    {
        $this->formCaptchaOptions = $formCaptchaOptions;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isLoginAfterRegistration()
    {
        return $this->loginAfterRegistration;
    }

    /**
     * @param boolean $loginAfterRegistration
     * @return RegisterOptions
     */
    public function setLoginAfterRegistration($loginAfterRegistration)
    {
        $this->loginAfterRegistration = $loginAfterRegistration;
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
     * @return RegisterOptions
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