<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/6/2016
 * Time: 7:21 PM
 */

namespace N3vrax\DkUser\Event;

use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Service\UserServiceInterface;
use Zend\EventManager\Event;
use Zend\Form\Form;

class RegisterEvent extends Event
{
    const EVENT_REGISTER_PRE = 'event.user.register.pre';
    const EVENT_REGISTER_POST = 'event.user.register.post';
    const EVENT_REGISTER_ERROR = 'event.user.register.error';

    /** @var UserServiceInterface  */
    protected $userService;

    /** @var  UserEntityInterface */
    protected $userEntity;

    /** @var  Form */
    protected $registerForm;

    /**
     * RegisterEvent constructor.
     * @param UserServiceInterface $userService
     * @param string $name
     */
    public function __construct(
        UserServiceInterface $userService,
        $name = self::EVENT_REGISTER_PRE)
    {
        parent::__construct($name);
        $this->userService = $userService;
    }

    /**
     * @return UserServiceInterface
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     * @param UserServiceInterface $userService
     * @return RegisterEvent
     */
    public function setUserService($userService)
    {
        $this->userService = $userService;
        return $this;
    }

    /**
     * @return UserEntityInterface
     */
    public function getUserEntity()
    {
        return $this->userEntity;
    }

    /**
     * @param UserEntityInterface $userEntity
     * @return RegisterEvent
     */
    public function setUserEntity($userEntity)
    {
        $this->userEntity = $userEntity;
        return $this;
    }

    /**
     * @return Form
     */
    public function getRegisterForm()
    {
        return $this->registerForm;
    }

    /**
     * @param Form $registerForm
     * @return RegisterEvent
     */
    public function setRegisterForm($registerForm)
    {
        $this->registerForm = $registerForm;
        return $this;
    }

}