<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:04 PM
 */

namespace N3vrax\DkUser\Service;

use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Event\ConfirmAccountEvent;
use N3vrax\DkUser\Event\ConfirmTokenGenerateEvent;
use N3vrax\DkUser\Event\Listener\UserListenerAwareInterface;
use N3vrax\DkUser\Event\Listener\UserListenerAwareTrait;
use N3vrax\DkUser\Event\PasswordResetEvent;
use N3vrax\DkUser\Event\RegisterEvent;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\ConfirmAccountOptions;
use N3vrax\DkUser\Options\PasswordRecoveryOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Result\ConfirmAccountResult;
use N3vrax\DkUser\Result\PasswordResetResult;
use N3vrax\DkUser\Result\RegisterResult;
use N3vrax\DkUser\Result\ResultInterface;
use Zend\Form\Form;
use Zend\Math\Rand;

class UserService implements UserServiceInterface, UserListenerAwareInterface
{
    use UserListenerAwareTrait;

    /** @var  UserMapperInterface */
    protected $userMapper;

    /** @var  UserOptions */
    protected $options;

    /** @var  Form */
    protected $registerForm;

    /** @var  Form */
    protected $resetPasswordForm;

    /** @var  UserEntityInterface */
    protected $userEntityPrototype;

    /** @var  PasswordInterface */
    protected $passwordService;

    /**
     * UserService constructor.
     * @param UserMapperInterface $userMapper
     * @param UserOptions $options
     * @param Form $registerForm
     * @param Form $resetPasswordForm
     * @param UserEntityInterface $userEntityPrototype
     * @param PasswordInterface $passwordService
     */
    public function __construct(
        UserMapperInterface $userMapper,
        UserOptions $options,
        Form $registerForm,
        Form $resetPasswordForm,
        UserEntityInterface $userEntityPrototype,
        PasswordInterface $passwordService
    )
    {
        $this->userMapper = $userMapper;
        $this->options = $options;
        $this->registerForm = $registerForm;
        $this->userEntityPrototype = $userEntityPrototype;
        $this->passwordService = $passwordService;
        $this->resetPasswordForm = $resetPasswordForm;

    }

    /**
     * Find user by its id
     *
     * @param $id
     * @return mixed
     */
    public function findUser($id)
    {
        return $this->userMapper->findUser($id);
    }

    /**
     * Get a user entity by some given field and value
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public function findUserBy($field, $value)
    {
        return $this->userMapper->findUserBy($field, $value);
    }

    /**
     * Gets all users from the backend
     *
     * @param array $filters
     * @return mixed
     */
    public function findAllUsers(array $filters = [])
    {
        return $this->userMapper->findAllUsers($filters);
    }

    /**
     * Return a paginated list of users based on some filters
     *
     * @param array $filters
     */
    public function findAllUsersPaginated(array $filters = [])
    {

    }

    /**
     * Save user is working as in create/update user, based on the presence of user id in the data
     *
     * @param $data
     * @return mixed
     */
    public function saveUser($data)
    {
        return $this->userMapper->saveUser($data);
    }

    /**
     * Remove an user based on its id
     *
     * @param $id
     * @return mixed
     */
    public function removeUser($id)
    {
        return $this->userMapper->removeUser($id);
    }

    /**
     * Get the last id generated
     *
     * @return mixed
     */
    public function getLastInsertValue()
    {
        return $this->userMapper->lastInsertValue();
    }

    /**
     * Change user status from unconfirmed to active based on an email and valid confirmation token
     *
     * @param $email
     * @param $token
     * @return ResultInterface
     * @throws \Exception
     */
    public function confirmAccount($email, $token)
    {
        $result = new ConfirmAccountResult(true, $this->options->getConfirmAccountOptions()
            ->getMessage(ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_SUCCESS));

        $user = null;

        try {
            if (empty($email) || empty($token)) {
                $result = $this->createConfirmAccountResultWithMessages(
                    $this->options->getConfirmAccountOptions()
                        ->getMessage(ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_MISSING_PARAMS)
                );
            } else {
                /** @var UserEntityInterface $user */
                $user = $this->findUserBy('email', $email);
                if ($user) {
                    $r = $this->userMapper->findConfirmToken($user->getId(), $token);
                    if ($r) {
                        //trigger pre event
                        $this->getEventManager()->triggerEvent(
                            $this->createConfirmAccountEvent($user, ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_PRE));

                        $this->userMapper->beginTransaction();

                        $user->setStatus($this->options->getConfirmAccountOptions()->getActiveUserStatus());
                        $this->saveUser($user);

                        $this->userMapper->disableConfirmToken($user->getId(), $token);

                        $this->userMapper->commit();

                        //post confirm event
                        $this->getEventManager()->triggerEvent(
                            $this->createConfirmAccountEvent($user, ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_POST));
                    } else {
                        $result = $this->createConfirmAccountResultWithMessages(
                            $this->options->getConfirmAccountOptions()
                                ->getMessage(ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_TOKEN)
                        );
                    }
                } else {
                    $result = $this->createConfirmAccountResultWithMessages(
                        $this->options->getConfirmAccountOptions()
                            ->getMessage(ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_EMAIL)
                    );
                }
            }
        } catch (\Exception $e) {
            error_log("Confirm account error: " . $e->getMessage(), E_USER_ERROR);

            $result = $this->createConfirmAccountResultWithException($e);
            //trigger error event
            $this->getEventManager()->triggerEvent(
                $this->createConfirmAccountEvent($user, ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_ERROR, $result));

            $this->userMapper->rollback();
            return $result;
        }

        if (!$result->isValid()) {
            $this->getEventManager()->triggerEvent(
                $this->createConfirmAccountEvent($user, ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_ERROR, $result));
        }

        return $result;
    }

    /**
     * Based on a user email, generate a token and store a hash of it with and expiration time
     * trigger a specific event, so mail service can send an email based on it
     *
     * @param $email
     * @return bool
     */
    public function resetPasswordRequest($email)
    {
        $result = new PasswordResetResult(true, $this->options->getPasswordRecoveryOptions()
            ->getMessage(PasswordRecoveryOptions::MESSAGE_FORGOT_PASSWORD_SUCCESS));

        $user = null;
        $data = null;

        if (empty($email)) {
            $result = $this->createPasswordResetResultWithMessages(
                $this->options->getPasswordRecoveryOptions()
                    ->getMessage(PasswordRecoveryOptions::MESSAGE_FORGOT_PASSWORD_MISSING_EMAIL));
        } else {
            try {
                /** @var UserEntityInterface $user */
                $user = $this->findUserBy('email', $email);

                if ($user) {
                    $data = new \stdClass();
                    $data->userId = $user->getId();
                    $data->token = md5(Rand::getString(32) . time() . $email);
                    $data->expireAt = time() + $this->options->getPasswordRecoveryOptions()
                            ->getResetPasswordTokenTimeout();

                    $this->getEventManager()->triggerEvent(
                        $this->createPasswordResetEvent(
                            $user,
                            PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_PRE,
                            $data
                        ));

                    $this->userMapper->saveResetToken((array)$data);

                    $this->getEventManager()->triggerEvent($this->createPasswordResetEvent(
                        $user,
                        PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_POST,
                        $data
                    ));
                }
            } catch (\Exception $e) {
                error_log("Password reset request error: " . $e->getMessage(), E_USER_ERROR);
                $result = $this->createPasswordResetResultTokenWithException($e);

                $this->getEventManager()->triggerEvent(
                    $this->createPasswordResetEvent(
                        $user,
                        PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_ERROR,
                        $data,
                        null,
                        $result
                    )
                );

                return $result;
            }
        }

        if (!$result->isValid()) {
            $this->getEventManager()->triggerEvent(
                $this->createPasswordResetEvent(
                    $user,
                    PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_ERROR,
                    $data,
                    null,
                    $result
                )
            );
        }

        return $result;
    }

    /**
     * @param $email
     * @param $token
     * @param $data
     * @return array
     */
    public function resetPassword($email, $token, $data)
    {
        $result = new PasswordResetResult(true, $this->options->getPasswordRecoveryOptions()
            ->getMessage(PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_SUCCESS));

        $user = null;
        $form = null;

        if (empty($email) || empty($token)) {
            $result = $this->createPasswordResetResultWithMessages($this->options->getPasswordRecoveryOptions()
                ->getMessage(PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_MISSING_PARAMS));

        }
        else {
            try {
                /** @var UserEntityInterface $user */
                $user = $this->userMapper->findUserBy('email', $email);
                if (!$user) {
                    $result = $this->createPasswordResetResultWithMessages($this->options->getPasswordRecoveryOptions()
                        ->getMessage(PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_INVALID_EMAIL));
                }
                else {
                    $r = $this->userMapper->findResetToken((int) $user->getId(), $token);
                    if ($r) {
                        $expireAt = $r['expireAt'];

                        if ($expireAt >= time()) {
                            $form = $this->resetPasswordForm;
                            $form->setData($data);

                            if ($form->isValid()) {
                                $data = $form->getData();
                                $user->setPassword($this->passwordService->create($data['newPassword']));

                                $this->getEventManager()->triggerEvent($this->createPasswordResetEvent(
                                    $user,
                                    PasswordResetEvent::EVENT_PASSWORD_RESET_PRE,
                                    $data,
                                    $form
                                ));

                                $this->saveUser($user);

                                $this->getEventManager()->triggerEvent($this->createPasswordResetEvent(
                                    $user,
                                    PasswordResetEvent::EVENT_PASSWORD_RESET_POST,
                                    $data,
                                    $form
                                ));
                            }
                            else {
                                $errors = [];
                                foreach ($form->getMessages() as $error) {
                                    $errors[] = current($error);
                                }

                                $result = $this->createPasswordResetResultWithMessages($errors);
                            }
                        }
                        else {
                            $result = $this->createPasswordResetResultWithMessages(
                                $this->options->getPasswordRecoveryOptions()
                                    ->getMessage(PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_TOKEN_EXPIRED));
                        }
                    }
                    else {
                        $result = $this->createPasswordResetResultWithMessages(
                            $this->options->getPasswordRecoveryOptions()
                                ->getMessage(PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_INVALID_TOKEN));
                    }
                }
            }
            catch (\Exception $e) {
                error_log("Password reset error: " . $e->getMessage());
                $result = $this->createPasswordResetResultWithException($e);

                $this->getEventManager()->triggerEvent(
                    $this->createPasswordResetEvent(
                        $user,
                        PasswordResetEvent::EVENT_PASSWORD_RESET_ERROR,
                        $data,
                        $form,
                        $result
                    )
                );
            }
        }

        if (!$result->isValid()) {
            $this->getEventManager()->triggerEvent(
                $this->createPasswordResetEvent(
                    $user,
                    PasswordResetEvent::EVENT_PASSWORD_RESET_ERROR,
                    $data,
                    $form,
                    $result
                )
            );
        }

        return $result;
    }

    /**
     * Store a new user into the db, after it validates the data
     * trigger register events
     *
     * @param $data
     * @return bool|UserEntityInterface
     */
    public function register($data)
    {
        $result = new RegisterResult(true, $this->options->getRegisterOptions()
            ->getMessage(RegisterOptions::MESSAGE_REGISTER_SUCCESS));

        $user = null;

        $form = $this->registerForm;
        $form->bind($this->userEntityPrototype);
        $form->setData($data);

        if (!$form->isValid()) {
            $messages = [];
            $formMessages = $form->getMessages();
            foreach ($formMessages as $message) {
                $messages[] = current($message);
            }

            $result = $this->createRegisterResultWithMessages($messages);
        } 
        else {

            $this->userMapper->beginTransaction();

            try {
                /** @var UserEntityInterface $user */
                $user = $form->getData();

                $user->setPassword($this->passwordService->create($user->getPassword()));
                if ($this->options->isEnableUserStatus()) {
                    $user->setStatus($this->options->getRegisterOptions()->getDefaultUserStatus());
                }

                //trigger pre register event
                $this->getEventManager()->triggerEvent(
                    $this->createRegisterEvent($user, $form, RegisterEvent::EVENT_REGISTER_PRE));

                $this->saveUser($user);

                //get newly created user id and save it to the object
                $id = $this->userMapper->lastInsertValue();
                if ($id) {
                    $user->setId($id);
                }

                $result->setUser($user);

                //generate a confirm token if enabled and also trigger events
                if ($this->options->getConfirmAccountOptions()->isEnableAccountConfirmation()) {
                    $this->generateConfirmToken($user);
                }

                //trigger post register event
                $this->getEventManager()->triggerEvent(
                    $this->createRegisterEvent($user, $form, RegisterEvent::EVENT_REGISTER_POST));

                $this->userMapper->commit();
            }
            catch (\Exception $e) {
                error_log("Register error: " . $e->getMessage(), E_USER_ERROR);

                $result = $this->createRegisterResultWithException($e);
                //trigger error event
                $this->getEventManager()->triggerEvent(
                    $this->createRegisterEvent($user, $form, RegisterEvent::EVENT_REGISTER_ERROR, $result));

                $this->userMapper->rollback();
                return $result;
            }
        }

        //if no exception but still some validation errors, trigger the error event
        if (!$result->isValid()) {
            $this->getEventManager()->triggerEvent(
                $this->createRegisterEvent($user, $form, RegisterEvent::EVENT_REGISTER_ERROR, $result));
        }

        return $result;

    }

    /**
     * @param UserEntityInterface $user
     * @throws \Exception
     */
    protected function generateConfirmToken(UserEntityInterface $user)
    {
        try {
            $data = new \stdClass();
            $data->userId = $user->getId();
            $data->token = md5(Rand::getString(32) . time() . $user->getEmail());

            $this->getEventManager()->triggerEvent(
                $this->createConfirmTokenGenerateEvent(
                    $user,
                    $data,
                    ConfirmTokenGenerateEvent::EVENT_GENERATE_CONFIRM_TOKEN_PRE));

            $this->userMapper->saveConfirmToken((array)$data);

            $this->getEventManager()->triggerEvent(
                $this->createConfirmTokenGenerateEvent(
                    $user,
                    $data,
                    ConfirmTokenGenerateEvent::EVENT_GENERATE_CONFIRM_TOKEN_POST
                )
            );
        } catch (\Exception $e) {
            error_log("Confirm token generation error: " . $e->getMessage());

            $this->getEventManager()->triggerEvent(
                $this->createConfirmTokenGenerateEvent(
                    $user,
                    null,
                    ConfirmTokenGenerateEvent::EVENT_GENERATE_CONFIRM_TOKEN_ERROR
                )
            );

            throw $e;
        }
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
     * @return UserService
     */
    public function setResetPasswordForm(Form $resetPasswordForm)
    {
        $this->resetPasswordForm = $resetPasswordForm;
        return $this;
    }

    /**
     * @return PasswordInterface
     */
    public function getPasswordService()
    {
        return $this->passwordService;
    }

    /**
     * @param PasswordInterface $passwordService
     * @return UserService
     */
    public function setPasswordService($passwordService)
    {
        $this->passwordService = $passwordService;
        return $this;
    }

    /**
     * @return UserMapperInterface
     */
    public function getUserMapper()
    {
        return $this->userMapper;
    }

    /**
     * @param UserMapperInterface $userMapper
     * @return UserService
     */
    public function setUserMapper($userMapper)
    {
        $this->userMapper = $userMapper;
        return $this;
    }

    /**
     * @return UserOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param UserOptions $options
     * @return UserService
     */
    public function setOptions($options)
    {
        $this->options = $options;
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
     * @return UserService
     */
    public function setRegisterForm(Form $registerForm)
    {
        $this->registerForm = $registerForm;
        return $this;
    }



    /**
     * @return UserEntityInterface
     */
    public function getUserEntityPrototype()
    {
        return $this->userEntityPrototype;
    }

    /**
     * @param UserEntityInterface $userEntityPrototype
     * @return UserService
     */
    public function setUserEntityPrototype($userEntityPrototype)
    {
        $this->userEntityPrototype = $userEntityPrototype;
        return $this;
    }

    protected function createConfirmAccountResultWithMessages($messages)
    {
        return new ConfirmAccountResult(false, $messages);
    }

    protected function createConfirmAccountResultWithException(\Exception $e)
    {
        return new ConfirmAccountResult(false, $this->options->getConfirmAccountOptions()
            ->getMessage(ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_ERROR), $e);
    }

    protected function createRegisterResultWithMessages($messages)
    {
        return new RegisterResult(false, $messages);
    }

    protected function createRegisterResultWithException(\Exception $e)
    {
        return new RegisterResult(false, $this->options->getRegisterOptions()
            ->getMessage(RegisterOptions::MESSAGE_REGISTER_ERROR), $e);
    }

    protected function createPasswordResetResultWithMessages($messages)
    {
        return new PasswordResetResult(false, $messages);
    }

    protected function createPasswordResetResultTokenWithException(\Exception $e)
    {
        return new PasswordResetResult(false, $this->options->getPasswordRecoveryOptions()
            ->getMessage(PasswordRecoveryOptions::MESSAGE_FORGOT_PASSWORD_ERROR), $e);
    }

    protected function createPasswordResetResultWithException(\Exception $e)
    {
        return new PasswordResetResult(false, $this->options->getPasswordRecoveryOptions()
            ->getMessage(PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_ERROR), $e);
    }

    protected function createConfirmAccountEvent(
        UserEntityInterface $user = null,
        $name = ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_PRE,
        ResultInterface $result = null
    )
    {
        $event = new ConfirmAccountEvent($this, $user, $name);
        if ($result) {
            $event->setResult($result);
        }

        return $event;
    }

    protected function createRegisterEvent(
        UserEntityInterface $user = null,
        Form $registerForm = null,
        $name = RegisterEvent::EVENT_REGISTER_PRE,
        ResultInterface $result = null
    )
    {
        $event = new RegisterEvent($this, $user, $name);
        if ($registerForm) {
            $event->setRegisterForm($registerForm);
        }
        if ($result) {
            $event->setResult($result);
        }

        return $event;
    }

    protected function createConfirmTokenGenerateEvent(
        UserEntityInterface $user = null,
        $data = null,
        $name = ConfirmTokenGenerateEvent::EVENT_GENERATE_CONFIRM_TOKEN_PRE
    )
    {
        $event = new ConfirmTokenGenerateEvent($this, $user, $data, $name);
        return $event;
    }

    protected function createPasswordResetEvent(
        UserEntityInterface $user = null,
        $name = PasswordResetEvent::EVENT_PASSWORD_RESET_PRE,
        $data = null,
        Form $resetPasswordForm = null,
        ResultInterface $result = null
    )
    {
        $event = new PasswordResetEvent($this, $user, $data, $name);
        if($resetPasswordForm) {
            $event->setResetPasswordForm($resetPasswordForm);
        }
        if ($result) {
            $event->setResult($result);
        }

        return $event;
    }

}