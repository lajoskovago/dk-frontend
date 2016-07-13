<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/13/2016
 * Time: 8:04 PM
 */

namespace N3vrax\DkUser\Event;

use N3vrax\DkBase\Event\Event;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Result\ResultInterface;
use N3vrax\DkUser\Service\UserServiceInterface;

class RememberTokenEvent extends Event
{
    const EVENT_TOKEN_GENERATE_PRE = 'event.user.remember_token.generate.pre';
    const EVENT_TOKEN_GENERATE_POST = 'event.user.remember_token.generate.post';
    const EVENT_TOKEN_GENERATE_ERROR = 'event.user.remember_token.generate.error';

    const EVENT_TOKEN_REMOVE_PRE = 'event.user.remember_token.remove.pre';
    const EVENT_TOKEN_REMOVE_POST = 'event.user.remember_token.remove.post';
    const EVENT_TOKEN_REMOVE_ERROR = 'event.user.remember_token.remove.error';

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  UserEntityInterface */
    protected $user;

    /** @var  mixed */
    protected $data;

    /** @var  ResultInterface */
    protected $result;

    public function __construct(
        UserServiceInterface $userService,
        $name,
        UserEntityInterface $user = null,
        $data = null,
        ResultInterface $result = null
    )
    {
        $this->userService = $userService;
        $this->user = $user;
        $this->data = $data;
        $this->result = $result;
        parent::__construct($name);
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
     * @return RememberTokenEvent
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
     * @return RememberTokenEvent
     */
    public function setUser($user)
    {
        $this->user = $user;
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
     * @return RememberTokenEvent
     */
    public function setData($data)
    {
        $this->data = $data;
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
     * @return RememberTokenEvent
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }


}