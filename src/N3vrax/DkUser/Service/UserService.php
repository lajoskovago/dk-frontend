<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:04 PM
 */

namespace N3vrax\DkUser\Service;

use N3vrax\DkUser\DkUser;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Event\ConfirmAccountEvent;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\ConfirmAccountOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Result\ConfirmAccountResult;
use N3vrax\DkUser\Result\RegisterResult;
use N3vrax\DkUser\Result\ResultInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Form;
use Zend\Math\Rand;

class UserService implements UserServiceInterface
{
    use EventManagerAwareTrait;

    const EVENT_RESET_PASSWORD_REQUEST = 'reset_password_request';
    const EVENT_RESET_PASSWORD_REQUEST_POST = 'reset_password_request.post';

    const EVENT_RESET_PASSWORD = 'reset_password';
    const EVENT_RESET_PASSWORD_POST = 'reset_password_post';

    const EVENT_CONFIRM_TOKEN = 'confirm_token';
    const EVENT_CONFIRM_TOKEN_POST = 'confirm_token_post';

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

        try{
            if(empty($email) || empty($token)) {
                $result = $this->createConfirmAccountResultWithMessages(
                    $this->options->getConfirmAccountOptions()
                    ->getMessage(ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_MISSING_PARAMS)
                );
            }
            else {
                /** @var UserEntityInterface $user */
                $user = $this->findUserBy('email', $email);
                if($user) {
                    $r = $this->userMapper->findConfirmToken($user->getId(), $token);
                    if($r) {
                        //trigger pre event
                        $this->getEventManager()->triggerEvent(
                            $this->createConfirmAccountEvent($user, ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_PRE));

                        $this->saveUser($user);

                        //post confirm event
                        $this->getEventManager()->triggerEvent(
                            $this->createConfirmAccountEvent($user, ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_POST));
                    }
                    else {
                        $result = $this->createConfirmAccountResultWithMessages(
                            $this->options->getConfirmAccountOptions()
                                ->getMessage(ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_TOKEN)
                        );
                    }
                }
                else {
                    $result = $this->createConfirmAccountResultWithMessages(
                        $this->options->getConfirmAccountOptions()
                            ->getMessage(ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_EMAIL)
                    );
                }
            }
        }
        catch(\Exception $e) {
            $result = $this->createConfirmAccountResultWithException($e);
            //trigger error event
            $this->getEventManager()->triggerEvent(
                $this->createConfirmAccountEvent($user, ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_ERROR, $result));
        }

        if(!$result->isValid()) {
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
        /** @var UserEntityInterface $user */
        $user = $this->findUserBy('email', $email);
        if($user) {
            $data = new \stdClass();
            $data->userId = $user->getId();
            $data->token = md5(Rand::getString(32) . time() . $email);
            $data->expireAt = time() + $this->options->getResetPasswordTokenTimeout();

            $this->getEventManager()->trigger(
                static::EVENT_RESET_PASSWORD_REQUEST,
                $this,
                ['data' => $data]
            );

            $this->userMapper->saveResetToken((array) $data);

            $this->getEventManager()->trigger(
                static::EVENT_RESET_PASSWORD_REQUEST_POST,
                $this,
                ['data' => $data]
            );

            return true;
        }

        return false;
    }

    /**
     * @param $email
     * @param $token
     * @param $data
     * @return array
     */
    public function resetPassword($email, $token, $data)
    {
        $errors = [];

        /** @var UserEntityInterface $user */
        $user = $this->userMapper->findUserBy('email', $email);

        if(!$user) {
            $errors[] = $this->options->getMessage(DkUser::MESSAGE_RESET_PASSWORD_INVALID_EMAIL);
        }
        else {
            $r = $this->userMapper->findResetToken($user->getId(), $token);
            if($r) {
                $t = $r['token'];
                $expireAt = $r['expireAt'];

                if($t == $token) {
                    if($expireAt >= time()) {
                        $form = $this->resetPasswordForm;
                        $form->setData($data);

                        if($form->isValid()) {
                            $data = $form->getData();
                            $user->setPassword($this->passwordService->create($data['newPassword']));

                            $this->getEventManager()->trigger(
                                static::EVENT_RESET_PASSWORD,
                                $this,
                                ['user' => $user, 'form' => $form]
                            );

                            $this->saveUser($user);

                            $this->getEventManager()->trigger(
                                static::EVENT_RESET_PASSWORD_POST,
                                $this,
                                ['user' => $user, 'form' => $form]
                            );
                        }
                        else {
                            foreach ($form->getMessages() as $error) {
                                $errors[] = current($error);
                            }
                        }
                    }
                    else {
                        $errors[] = $this->options->getMessage(DkUser::MESSAGE_RESET_PASSWORD_TOKEN_EXPIRED);
                    }
                }
                else {
                    $errors[] = $this->options->getMessage(DkUser::MESSAGE_RESET_PASSWORD_INVALID_TOKEN);
                }
            }
            else {
                $errors[] = $this->options->getMessage(DkUser::MESSAGE_RESET_PASSWORD_INVALID_TOKEN);
            }
        }

        return $errors;
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

        $form = $this->registerForm;
        $form->bind($this->userEntityPrototype);
        $form->setData($data);

        if(!$form->isValid()) {
            $messages = [];
            $formMessages = $form->getMessages();
            foreach ($formMessages as $message) {
                $messages[] = current($message);
            }

            $result = $this->createRegisterResultWithMessages($messages);
        }
        else {
            /** @var UserEntityInterface $user */
            $user = $form->getData();

            $user->setPassword($this->passwordService->create($user->getPassword()));
            if($this->options->isEnableUserStatus()) {
                $user->setStatus($this->options->getRegisterOptions()->getDefaultUserStatus());
            }

            $this->getEventManager()->trigger($this->createRegisterEvent());

            $this->saveUser($user);
            $id = $this->userMapper->lastInsertValue();
            if($id) {
                $user->setId($id);
            }

            if($this->registerOptions->isEnableAccountConfirmation()) {
                $this->generateConfirmToken($user);
            }

            $this->getEventManager()->trigger(static::EVENT_REGISTER_POST, $this,
                ['user' => $user, 'form' => $form]);

            return $user;
        }


    }

    protected function generateConfirmToken(UserEntityInterface $user)
    {
        $data = new \stdClass();
        $data->userId = $user->getId();
        $data->token = md5(Rand::getString(32) . time() . $user->getEmail());

        $this->getEventManager()->trigger(static::EVENT_CONFIRM_TOKEN, $this, ['data' => $data]);

        $this->userMapper->saveConfirmToken((array) $data);

        $this->getEventManager()->trigger(static::EVENT_CONFIRM_TOKEN_POST, $this, ['data' => $data]);
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
    public function setResetPasswordForm($resetPasswordForm)
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
    public function setRegisterForm($registerForm)
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

    protected function createConfirmAccountEvent(
        UserEntityInterface $user = null,
        $name = ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_PRE,
        ResultInterface $result = null
    ) {
        $event = new ConfirmAccountEvent($this, $user, $name);
        if($result) {
            $event->setResult($result);
        }

        return $event;
    }

    protected function createRegisterEvent()
    {

    }
    
}