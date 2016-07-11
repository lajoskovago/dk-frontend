<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/7/2016
 * Time: 7:24 PM
 */

namespace N3vrax\DkUser\Event;

use N3vrax\DkBase\Event\Event;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Service\UserServiceInterface;

class ConfirmTokenGenerateEvent extends Event
{
    const EVENT_GENERATE_CONFIRM_TOKEN_PRE = 'event.user.generate_confirm_token.pre';
    const EVENT_GENERATE_CONFIRM_TOKEN_POST = 'event.user.generate_confirm_token.post';
    const EVENT_GENERATE_CONFIRM_TOKEN_ERROR = 'event.user.generate_confirm_token.error';

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  UserEntityInterface */
    protected $user;

    /** @var  object */
    protected $data;

    public function __construct(
        UserServiceInterface $userService,
        UserEntityInterface $user = null,
        $data = null,
        $name = self::EVENT_GENERATE_CONFIRM_TOKEN_PRE)
    {
        parent::__construct($name);
        $this->userService = $userService;
        $this->user = $user;
        $this->data = $data;
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
     * @return ConfirmTokenGenerateEvent
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
     * @return ConfirmTokenGenerateEvent
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param object $data
     * @return ConfirmTokenGenerateEvent
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }



}