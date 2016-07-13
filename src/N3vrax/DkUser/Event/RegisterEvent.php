<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/6/2016
 * Time: 7:21 PM
 */

namespace N3vrax\DkUser\Event;

use N3vrax\DkBase\Event\Event;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Result\ResultInterface;
use N3vrax\DkUser\Service\UserServiceInterface;
use Zend\Form\Form;

class RegisterEvent extends Event
{
    const EVENT_REGISTER_PRE = 'event.user.register.pre';
    const EVENT_REGISTER_POST = 'event.user.register.post';
    const EVENT_REGISTER_ERROR = 'event.user.register.error';

    /** @var UserServiceInterface  */
    protected $userService;

    /** @var  UserEntityInterface */
    protected $user;

    /** @var  Form */
    protected $registerForm;

    /** @var  ResultInterface */
    protected $result;

    /**
     * RegisterEvent constructor.
     * @param UserServiceInterface $userService
     * @param UserEntityInterface $user
     * @param string $name
     */
    public function __construct(
        UserServiceInterface $userService,
        $name = self::EVENT_REGISTER_PRE,
        UserEntityInterface $user = null
        )
    {
        parent::__construct($name);
        $this->userService = $userService;
        $this->user = $user;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserEntityInterface $user
     * @return RegisterEvent
     */
    public function setUser($user)
    {
        $this->user = $user;
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

    /**
     * @return ResultInterface
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param ResultInterface $result
     * @return RegisterEvent
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }


}