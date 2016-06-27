<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:08 PM
 */

namespace N3vrax\DkUser\Options;

use N3vrax\DkUser\Entity\UserEntity;
use N3vrax\DkUser\Entity\UserEntityHydrator;
use N3vrax\DkUser\Form\LoginForm;
use N3vrax\DkUser\Form\RegisterForm;
use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /** @var  string */
    protected $zendDbAdapter;

    /** @var  string */
    protected $userEntityClass = UserEntity::class;

    /** @var  string */
    protected $userEntityHydrator = UserEntityHydrator::class;

    /** @var  string */
    protected $loginForm = LoginForm::class;

    /** @var  string */
    protected $registerForm = RegisterForm::class;

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
     * @return string
     */
    public function getLoginForm()
    {
        return $this->loginForm;
    }

    /**
     * @param string $loginForm
     * @return ModuleOptions
     */
    public function setLoginForm($loginForm)
    {
        $this->loginForm = $loginForm;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegisterForm()
    {
        return $this->registerForm;
    }

    /**
     * @param string $registerForm
     * @return ModuleOptions
     */
    public function setRegisterForm($registerForm)
    {
        $this->registerForm = $registerForm;
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
    
    
}