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
    protected $userTableName = 'user';

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
    public function getUserTableName()
    {
        return $this->userTableName;
    }

    /**
     * @param string $userTableName
     * @return ModuleOptions
     */
    public function setUserTableName($userTableName)
    {
        $this->userTableName = $userTableName;
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

    
}