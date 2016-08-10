<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/5/2016
 * Time: 11:40 PM
 */

namespace Frontend\User\Service;

use Frontend\User\Event\UserUpdateEvent;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Result\ResultInterface;
use N3vrax\DkUser\Result\UserOperationResult;

class UserService extends \N3vrax\DkUser\Service\UserService implements UserServiceInterface
{
    /**
     * @param UserEntityInterface $user
     * @return UserOperationResult
     */
    public function updateAccountInfo(UserEntityInterface $user)
    {
        $result = new UserOperationResult(true, 'Account successfully updated');

        try {
            $this->userMapper->beginTransaction();

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_PRE, $user));
            
            $this->saveUser($user);
            
            $result->setUser($user);

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_POST, $user));

            $this->userMapper->commit();
        }
        catch(\Exception $e) {
            error_log('Update user error: ' . $e->getMessage());
            $result = $this->createUserOperationResultWithException(
                $e, 'Account update failed. Please try again', $user);

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_ERROR, $user, $result));

            $this->userMapper->rollback();
        }

        return $result;
    }

    protected function createUpdateEvent(
        $name = UserUpdateEvent::EVENT_UPDATE_PRE,
        UserEntityInterface $user = null,
        ResultInterface $result = null)
    {
        $event = new UserUpdateEvent($this, $name, $user, $result);
        return $this->setupEventPsr7Messages($event);
    }
}