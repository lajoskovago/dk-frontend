<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/8/2016
 * Time: 6:59 PM
 */

namespace N3vrax\DkUser\Event;

use N3vrax\DkBase\Event\Event;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Result\ResultInterface;
use N3vrax\DkUser\Service\UserServiceInterface;
use Zend\Form\Form;

class PasswordResetEvent extends Event
{
    const EVENT_PASSWORD_RESET_TOKEN_PRE = 'event.user.password_reset.token.pre';
    const EVENT_PASSWORD_RESET_TOKEN_POST = 'event.user.password_reset.token.post';
    const EVENT_PASSWORD_RESET_TOKEN_ERROR = 'event.user.password_reset.token.error';

    const EVENT_PASSWORD_RESET_PRE = 'event.user.password_reset.pre';
    const EVENT_PASSWORD_RESET_POST = 'event.user.password_reset.post';
    const EVENT_PASSWORD_RESET_ERROR = 'event.user.password_reset.error';

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  UserEntityInterface */
    protected $user;

    /** @var  mixed */
    protected $data;

    /** @var  Form */
    protected $resetPasswordForm;

    /** @var  ResultInterface */
    protected $result;

    /**
     * PasswordResetEvent constructor.
     * @param UserServiceInterface $userService
     * @param UserEntityInterface|null $user
     * @param object $data
     * @param string $name
     * @param ResultInterface|null $result
     */
    public function __construct(
        UserServiceInterface $userService,
        $name = self::EVENT_PASSWORD_RESET_PRE,
        UserEntityInterface $user = null,
        $data = null,
        ResultInterface $result = null)
    {
        parent::__construct($name);
        $this->userService = $userService;
        $this->data = $data;
        $this->user = $user;
        $this->result = $result;
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
     * @return PasswordResetEvent
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
     * @return PasswordResetEvent
     */
    public function setUser($user)
    {
        $this->user = $user;
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
     * @return PasswordResetEvent
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return Form
     */
    public function getResetPasswordForm()
    {
        return $this->resetPasswordForm;
    }

    /**
     * @param Form $resetPasswordForm
     * @return PasswordResetEvent
     */
    public function setResetPasswordForm($resetPasswordForm)
    {
        $this->resetPasswordForm = $resetPasswordForm;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return PasswordResetEvent
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    

}