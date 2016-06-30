<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:08 PM
 */

namespace N3vrax\DkUser\Options;

use N3vrax\DkUser\DkUser;
use N3vrax\DkUser\Entity\UserEntity;
use N3vrax\DkUser\Entity\UserEntityHydrator;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

class ModuleOptions extends AbstractOptions
{
    /** @var  string */
    protected $zendDbAdapter;

    /** @var  string */
    protected $userEntityClass = UserEntity::class;

    /** @var  string */
    protected $userEntityHydrator = UserEntityHydrator::class;

    /** @var bool  */
    protected $loginAfterRegistration = false;

    /** @var bool  */
    protected $enableRegistration = true;

    /** @var bool  */
    protected $enablePasswordRecovery = true;

    /** @var int  */
    protected $resetPasswordTokenTimeout = 3600;

    /** @var int  */
    protected $passwordCost = 11;

    /** @var bool  */
    protected $enableUserStatus = true;

    /** @var string  */
    protected $activeUserStatus = 'active';

    /** @var string  */
    protected $notConfirmedUserStatus = 'pending';
    
    /** @var array  */
    protected $messages = [
        DkUser::MESSAGE_CONFIRM_ACCOUNT_DISABLED => 'Account confirmation is disabled',
        DkUser::MESSAGE_CONFIRM_ACCOUNT_ERROR => 'Account confirmation error. Please try again',
        DkUser::MESSAGE_CONFIRM_ACCOUNT_INVALID_EMAIL => 'Account confirmation invalid parameters',
        DkUser::MESSAGE_CONFIRM_ACCOUNT_INVALID_TOKEN => 'Account confirmation invalid parameters',
        DkUser::MESSAGE_CONFIRM_ACCOUNT_MISSING_PARAMS => 'Account confirmation invalid parameters',
        DkUser::MESSAGE_CONFIRM_ACCOUNT_INVALID_ACCOUNT => 'Current account status does not allow confirmation',
        DkUser::MESSAGE_CONFIRM_ACCOUNT_SUCCESS => 'Account successfully confirmed. You may sign in now',

        DkUser::MESSAGE_FORGOT_PASSWORD_ERROR => 'Password reset request error. Please try again',
        DkUser::MESSAGE_FORGOT_PASSWORD_MISSING_EMAIL => 'Email address is required and cannot be empty',
        DkUser::MESSAGE_FORGOT_PASSWORD_SUCCESS => [
            'Password reset request successfully registered',
            'You\'ll receive in email with further instructions'
        ],

        DkUser::MESSAGE_RESET_PASSWORD_DISABLED => 'Password recovery is disabled',
        DkUser::MESSAGE_RESET_PASSWORD_ERROR => 'Password reset error. Please try again',
        DkUser::MESSAGE_RESET_PASSWORD_INVALID_EMAIL => 'Password reset error. Invalid parameters',
        DkUser::MESSAGE_RESET_PASSWORD_INVALID_TOKEN => 'Password reset error. Invalid parameters',
        DkUser::MESSAGE_RESET_PASSWORD_MISSING_PARAMS => 'Password reset error. Invalid parameters',
        DkUser::MESSAGE_RESET_PASSWORD_TOKEN_EXPIRED => 'Password reset error. Reset token has expired',
        DkUser::MESSAGE_RESET_PASSWORD_SUCCESS => 'Account password successfully updated',

        DkUser::MESSAGE_REGISTER_EMPTY_EMAIL => 'Email address is required and cannot be empty',
        DkUser::MESSAGE_REGISTER_INVALID_EMAIL => 'Email address format is not valid',
        DkUser::MESSAGE_REGISTER_EMAIL_ALREADY_REGISTERED => 'Email address is already in use',
        DkUser::MESSAGE_REGISTER_EMPTY_USERNAME => 'Username is required and cannot be empty',
        DkUser::MESSAGE_REGISTER_USERNAME_TOO_SHORT => 'Username must have at least 4 characters',
        DkUser::MESSAGE_REGISTER_USERNAME_INVALID_CHARACTERS => 'Username contains invalid characters',
        DkUser::MESSAGE_REGISTER_USERNAME_ALREADY_REGISTERED => 'Username is already in use',
        DkUser::MESSAGE_REGISTER_EMPTY_PASSWORD => 'Password is required and cannot be empty',
        DkUser::MESSAGE_REGISTER_PASSWORD_TOO_SHORT => 'Password must have at least 4 characters',
        DkUser::MESSAGE_REGISTER_EMPTY_PASSWORD_CONFIRM => 'Password confirmation is required',
        DkUser::MESSAGE_REGISTER_PASSWORD_CONFIRM_NOT_MATCH => 'The two passwords do not match',

        DkUser::MESSAGE_LOGIN_EMPTY_IDENTITY => 'Identity is required and cannot be empty',
        DkUser::MESSAGE_LOGIN_EMPTY_PASSWORD => 'Password is required and cannot be empty',
        DkUser::MESSAGE_LOGIN_PASSWORD_TOO_SHORT => 'Password must have at least 4 characters',
    ];

    /** @var bool */
    protected $__strictMode__ = false;

    /**
     * @return string
     */
    public function getZendDbAdapter()
    {
        return $this->zendDbAdapter;
    }

    /**
     * @param string $zendDbAdapter
     * @return ModuleOptions
     */
    public function setZendDbAdapter($zendDbAdapter)
    {
        $this->zendDbAdapter = $zendDbAdapter;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserEntityClass()
    {
        return $this->userEntityClass;
    }

    /**
     * @param string $userEntityClass
     * @return ModuleOptions
     */
    public function setUserEntityClass($userEntityClass)
    {
        $this->userEntityClass = $userEntityClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserEntityHydrator()
    {
        return $this->userEntityHydrator;
    }

    /**
     * @param string $userEntityHydrator
     * @return ModuleOptions
     */
    public function setUserEntityHydrator($userEntityHydrator)
    {
        $this->userEntityHydrator = $userEntityHydrator;
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
     * @return ModuleOptions
     */
    public function setLoginAfterRegistration($loginAfterRegistration)
    {
        $this->loginAfterRegistration = $loginAfterRegistration;
        return $this;
    }

    /**
     * @return int
     */
    public function getPasswordCost()
    {
        return $this->passwordCost;
    }

    /**
     * @param int $passwordCost
     * @return ModuleOptions
     */
    public function setPasswordCost($passwordCost)
    {
        $this->passwordCost = $passwordCost;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnableUserStatus()
    {
        return $this->enableUserStatus;
    }

    /**
     * @param boolean $enableUserStatus
     * @return ModuleOptions
     */
    public function setEnableUserStatus($enableUserStatus)
    {
        $this->enableUserStatus = $enableUserStatus;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnableRegistration()
    {
        return $this->enableRegistration;
    }

    /**
     * @param boolean $enableRegistration
     * @return ModuleOptions
     */
    public function setEnableRegistration($enableRegistration)
    {
        $this->enableRegistration = $enableRegistration;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnablePasswordRecovery()
    {
        return $this->enablePasswordRecovery;
    }

    /**
     * @param boolean $enablePasswordRecovery
     * @return ModuleOptions
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
     * @return ModuleOptions
     */
    public function setResetPasswordTokenTimeout($resetPasswordTokenTimeout)
    {
        $this->resetPasswordTokenTimeout = $resetPasswordTokenTimeout;
        return $this;
    }

    /**
     * @return string
     */
    public function getActiveUserStatus()
    {
        return $this->activeUserStatus;
    }

    /**
     * @param string $activeUserStatus
     * @return ModuleOptions
     */
    public function setActiveUserStatus($activeUserStatus)
    {
        $this->activeUserStatus = $activeUserStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotConfirmedUserStatus()
    {
        return $this->notConfirmedUserStatus;
    }

    /**
     * @param string $notConfirmedUserStatus
     * @return ModuleOptions
     */
    public function setNotConfirmedUserStatus($notConfirmedUserStatus)
    {
        $this->notConfirmedUserStatus = $notConfirmedUserStatus;
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
     * @return ModuleOptions
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
        return isset($this->messages[$key]) ? $this->messages[$key] : 'Missing message key';
    }
    
    
}