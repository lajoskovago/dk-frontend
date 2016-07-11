<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/6/2016
 * Time: 7:34 PM
 */

namespace N3vrax\DkUser\Event;

use N3vrax\DkBase\Event\Event;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Result\ResultInterface;
use N3vrax\DkUser\Service\UserServiceInterface;

class ConfirmAccountEvent extends Event
{
    const EVENT_CONFIRM_ACCOUNT_PRE = 'event.user.confirm_account.pre';
    const EVENT_CONFIRM_ACCOUNT_POST = 'event.user.confirm_account.post';
    const EVENT_CONFIRM_ACCOUNT_ERROR = 'event.user.confirm_account.error';

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  UserEntityInterface */
    protected $userEntity;

    /** @var  ResultInterface */
    protected $result;

    /**
     * ConfirmAccountEvent constructor.
     * @param UserServiceInterface $userService
     * @param UserEntityInterface $userEntity
     * @param string $name
     */
    public function __construct(
        UserServiceInterface $userService,
        UserEntityInterface $userEntity = null,
        $name = self::EVENT_CONFIRM_ACCOUNT_PRE)
    {
        parent::__construct($name);
        $this->userService = $userService;
        $this->userEntity = $userEntity;
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
     * @return ConfirmAccountEvent
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
     * @return ConfirmAccountEvent
     */
    public function setUserEntity($userEntity)
    {
        $this->userEntity = $userEntity;
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
     * @return ConfirmAccountEvent
     */
    public function setResult(ResultInterface $result)
    {
        $this->result = $result;
        return $this;
    }



}