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
use N3vrax\DkUser\Event\RememberTokenEvent;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\ConfirmAccountOptions;
use N3vrax\DkUser\Options\LoginOptions;
use N3vrax\DkUser\Options\PasswordRecoveryOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Result\ConfirmAccountResult;
use N3vrax\DkUser\Result\PasswordResetResult;
use N3vrax\DkUser\Result\RegisterResult;
use N3vrax\DkUser\Result\RememberTokenResult;
use N3vrax\DkUser\Result\ResultInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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

    /** @var  ServerRequestInterface */
    protected $request;

    /** @var  ResponseInterface */
    protected $response;

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
     * Generates an auto-login token for the user, stores it in the backend and sets a login cookie
     *
     * @param UserEntityInterface $user
     * @return RememberTokenResult
     */
    public function generateRememberToken(UserEntityInterface $user)
    {
        $result = new RememberTokenResult(true);
        $data = null;

        try{
            $selector = Rand::getString(32);
            $token = Rand::getString(32);

            $data = new \stdClass();
            $data->userId = $user->getId();
            $data->selector = $selector;
            $data->token = $token;

            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_GENERATE_PRE,
                $user,
                $data
            ));

            //hash the token
            $dbData = (array) $data;
            $dbData['token'] = md5($dbData['token']);

            $this->userMapper->saveRememberToken($dbData);

            $cookieData = base64_encode(serialize(['selector' => $selector, 'token' => $token]));

            $name = $this->options->getLoginOptions()->getRememberMeCookieName();
            $expire = $this->options->getLoginOptions()->getRememberMeCookieExpire();
            $secure = $this->options->getLoginOptions()->isRememberMeCookieSecure();

            setcookie($name, $cookieData, time() + $expire, "/", "", $secure, true);

            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_GENERATE_POST,
                $user,
                $data
            ));
        }
        catch (\Exception $e) {
            error_log("Remember token generate error: " . $e->getMessage());
            $result = $this->createRememberTokenResultGenerateWithException($e);

            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_GENERATE_ERROR,
                $user,
                $data,
                $result
            ));
        }

        return $result;
    }

    /**
     * Validates a remember token coming from cookie
     *
     * @param $selector
     * @param $token
     * @return bool
     */
    public function checkRememberToken($selector, $token)
    {
        try{
            $r = $this->userMapper->findRememberToken($selector);
            if($r) {
                if($r['token'] == md5($token)) {
                    return $r;
                }
                else {
                    //clear any tokens for this user as security measure
                    $user = $this->findUser($r['userId']);
                    if($user) {
                        $this->removeRememberToken($user);
                    }
                }
            }
        }
        catch(\Exception $e) {
            error_log("Check remember token error: " . $e->getMessage());
            return false;
        }

        return false;
    }

    /**
     * Removes all remember tokens for a given user and also unset the corresponding cookie
     *
     * @param UserEntityInterface $user
     * @return RememberTokenResult
     */
    public function removeRememberToken(UserEntityInterface $user)
    {
        $result = new RememberTokenResult(true);
        try{
            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_REMOVE_PRE,
                $user
            ));

            $this->userMapper->removeRememberToken($user->getId());

            //clear cookies
            if(isset($_COOKIE[$this->options->getLoginOptions()->getRememberMeCookieName()])) {
                unset($_COOKIE[$this->options->getLoginOptions()->getRememberMeCookieName()]);
                setcookie($this->options->getLoginOptions()->getRememberMeCookieName(), '', time() - 3600, '/');
            }

            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_REMOVE_POST,
                $user
            ));
        }
        catch(\Exception $e) {
            error_log("Remove remember token error for user " . $user->getId() . " with message: " . $e->getMessage());
            $result = $this->createRememberTokenResultRemoveWithException($e);
            
            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_REMOVE_ERROR,
                $user,
                null,
                $result
            ));
        }

        return $result;
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
                            $this->createConfirmAccountEvent(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_PRE, $user));

                        $this->userMapper->beginTransaction();

                        $user->setStatus($this->options->getConfirmAccountOptions()->getActiveUserStatus());
                        $this->saveUser($user);

                        $this->userMapper->disableConfirmToken($user->getId(), $token);

                        $this->userMapper->commit();

                        //post confirm event
                        $this->getEventManager()->triggerEvent(
                            $this->createConfirmAccountEvent(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_POST, $user));
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
        catch (\Exception $e) {
            error_log("Confirm account error: " . $e->getMessage(), E_USER_ERROR);

            $result = $this->createConfirmAccountResultWithException($e);
            //trigger error event
            $this->getEventManager()->triggerEvent(
                $this->createConfirmAccountEvent(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_ERROR, $user, $result));

            $this->userMapper->rollback();
            return $result;
        }

        if (!$result->isValid()) {
            $this->getEventManager()->triggerEvent(
                $this->createConfirmAccountEvent(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_ERROR, $user, $result));
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
                            PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_PRE,
                            $user,
                            $data
                        ));

                    $this->userMapper->saveResetToken((array)$data);

                    $this->getEventManager()->triggerEvent($this->createPasswordResetEvent(
                        PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_POST,
                        $user,
                        $data
                    ));
                }
            } catch (\Exception $e) {
                error_log("Password reset request error: " . $e->getMessage(), E_USER_ERROR);
                $result = $this->createPasswordResetResultTokenWithException($e);

                $this->getEventManager()->triggerEvent(
                    $this->createPasswordResetEvent(
                        PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_ERROR,
                        $user,
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
                    PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_ERROR,
                    $user,
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
                                    PasswordResetEvent::EVENT_PASSWORD_RESET_PRE,
                                    $user,
                                    $data,
                                    $form
                                ));

                                $this->saveUser($user);

                                $this->getEventManager()->triggerEvent($this->createPasswordResetEvent(
                                    PasswordResetEvent::EVENT_PASSWORD_RESET_POST,
                                    $user,
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
                        PasswordResetEvent::EVENT_PASSWORD_RESET_ERROR,
                        $user,
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
                    PasswordResetEvent::EVENT_PASSWORD_RESET_ERROR,
                    $user,
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
                    $this->createRegisterEvent(RegisterEvent::EVENT_REGISTER_PRE, $user, $form));

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
                    $this->createRegisterEvent(RegisterEvent::EVENT_REGISTER_POST, $user, $form));

                $this->userMapper->commit();
            }
            catch (\Exception $e) {
                error_log("Register error: " . $e->getMessage(), E_USER_ERROR);

                $result = $this->createRegisterResultWithException($e);
                //trigger error event
                $this->getEventManager()->triggerEvent(
                    $this->createRegisterEvent(RegisterEvent::EVENT_REGISTER_ERROR, $user, $form, $result));

                $this->userMapper->rollback();
                return $result;
            }
        }

        //if no exception but still some validation errors, trigger the error event
        if (!$result->isValid()) {
            $this->getEventManager()->triggerEvent(
                $this->createRegisterEvent(RegisterEvent::EVENT_REGISTER_ERROR, $user, $form, $result));
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
                    ConfirmTokenGenerateEvent::EVENT_GENERATE_CONFIRM_TOKEN_PRE,
                    $user,
                    $data
                ));

            $this->userMapper->saveConfirmToken((array)$data);

            $this->getEventManager()->triggerEvent(
                $this->createConfirmTokenGenerateEvent(
                    ConfirmTokenGenerateEvent::EVENT_GENERATE_CONFIRM_TOKEN_POST,
                    $user,
                    $data
                ));

        } catch (\Exception $e) {
            error_log("Confirm token generation error: " . $e->getMessage());

            $this->getEventManager()->triggerEvent(
                $this->createConfirmTokenGenerateEvent(
                    ConfirmTokenGenerateEvent::EVENT_GENERATE_CONFIRM_TOKEN_ERROR,
                    $user,
                    null
                ));

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
    public function setOptions(UserOptions $options)
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

    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param ServerRequestInterface $request
     * @return UserService
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     * @return UserService
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @param $messages
     * @return ConfirmAccountResult
     */
    protected function createConfirmAccountResultWithMessages($messages)
    {
        return new ConfirmAccountResult(false, $messages);
    }

    /**
     * @param \Exception $e
     * @return ConfirmAccountResult
     */
    protected function createConfirmAccountResultWithException(\Exception $e)
    {
        return new ConfirmAccountResult(false, $this->options->getConfirmAccountOptions()
            ->getMessage(ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_ERROR), $e);
    }

    /**
     * @param $messages
     * @return RegisterResult
     */
    protected function createRegisterResultWithMessages($messages)
    {
        return new RegisterResult(false, $messages);
    }

    /**
     * @param \Exception $e
     * @return RegisterResult
     */
    protected function createRegisterResultWithException(\Exception $e)
    {
        return new RegisterResult(false, $this->options->getRegisterOptions()
            ->getMessage(RegisterOptions::MESSAGE_REGISTER_ERROR), $e);
    }

    /**
     * @param $messages
     * @return PasswordResetResult
     */
    protected function createPasswordResetResultWithMessages($messages)
    {
        return new PasswordResetResult(false, $messages);
    }

    /**
     * @param \Exception $e
     * @return PasswordResetResult
     */
    protected function createPasswordResetResultTokenWithException(\Exception $e)
    {
        return new PasswordResetResult(false, $this->options->getPasswordRecoveryOptions()
            ->getMessage(PasswordRecoveryOptions::MESSAGE_FORGOT_PASSWORD_ERROR), $e);
    }

    /**
     * @param \Exception $e
     * @return PasswordResetResult
     */
    protected function createPasswordResetResultWithException(\Exception $e)
    {
        return new PasswordResetResult(false, $this->options->getPasswordRecoveryOptions()
            ->getMessage(PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_ERROR), $e);
    }

    /**
     * @param $messages
     * @return RememberTokenResult
     */
    protected function createRememberTokenResultWithMessages($messages)
    {
        return new RememberTokenResult(false, $messages);
    }

    /**
     * @param \Exception $e
     * @return RememberTokenResult
     */
    protected function createRememberTokenResultGenerateWithException(\Exception $e)
    {
        return new RememberTokenResult(false, $this->options->getLoginOptions()
            ->getMessage(LoginOptions::MESSAGE_REMEMBER_TOKEN_GENERATE_ERROR), $e);
    }

    /**
     * @param \Exception $e
     * @return RememberTokenResult
     */
    protected function createRememberTokenResultRemoveWithException(\Exception $e)
    {
        return new RememberTokenResult(false, $this->options->getLoginOptions()
            ->getMessage(LoginOptions::MESSAGE_REMEMBER_TOKEN_REMOVE_ERROR), $e);
    }

    /**
     * @param string $name
     * @param UserEntityInterface|null $user
     * @param ResultInterface|null $result
     * @return ConfirmAccountEvent
     */
    protected function createConfirmAccountEvent(
        $name = ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_PRE,
        UserEntityInterface $user = null,
        ResultInterface $result = null
    )
    {
        $event = new ConfirmAccountEvent($this, $name, $user);
        if($this->request) {
            $event->setRequest($this->request);
        }
        if($this->response) {
            $event->setResponse($this->response);
        }
        if ($result) {
            $event->setResult($result);
        }

        return $event;
    }

    /**
     * @param string $name
     * @param UserEntityInterface|null $user
     * @param Form|null $registerForm
     * @param ResultInterface|null $result
     * @return RegisterEvent
     */
    protected function createRegisterEvent(
        $name = RegisterEvent::EVENT_REGISTER_PRE,
        UserEntityInterface $user = null,
        Form $registerForm = null,
        ResultInterface $result = null
    )
    {
        $event = new RegisterEvent($this, $name, $user);
        if ($registerForm) {
            $event->setRegisterForm($registerForm);
        }
        if($this->request) {
            $event->setRequest($this->request);
        }
        if($this->response) {
            $event->setResponse($this->response);
        }
        if ($result) {
            $event->setResult($result);
        }

        return $event;
    }

    /**
     * @param string $name
     * @param UserEntityInterface|null $user
     * @param null $data
     * @return ConfirmTokenGenerateEvent
     */
    protected function createConfirmTokenGenerateEvent(
        $name = ConfirmTokenGenerateEvent::EVENT_GENERATE_CONFIRM_TOKEN_PRE,
        UserEntityInterface $user = null,
        $data = null
    )
    {
        $event = new ConfirmTokenGenerateEvent($this, $name, $user, $data);
        if($this->request) {
            $event->setRequest($this->request);
        }
        if($this->response) {
            $event->setResponse($this->response);
        }
        return $event;
    }

    /**
     * @param string $name
     * @param UserEntityInterface|null $user
     * @param null $data
     * @param Form|null $resetPasswordForm
     * @param ResultInterface|null $result
     * @return PasswordResetEvent
     */
    protected function createPasswordResetEvent(
        $name = PasswordResetEvent::EVENT_PASSWORD_RESET_PRE,
        UserEntityInterface $user = null,
        $data = null,
        Form $resetPasswordForm = null,
        ResultInterface $result = null
    )
    {
        $event = new PasswordResetEvent($this, $name, $user, $data);

        if($resetPasswordForm) {
            $event->setResetPasswordForm($resetPasswordForm);
        }
        if($this->request) {
            $event->setRequest($this->request);
        }
        if($this->response) {
            $event->setResponse($this->response);
        }
        if ($result) {
            $event->setResult($result);
        }

        return $event;
    }

    /**
     * @param string $name
     * @param UserEntityInterface|null $user
     * @param null $data
     * @param ResultInterface|null $result
     * @return RememberTokenEvent
     */
    protected function createRememberTokenEvent(
        $name = RememberTokenEvent::EVENT_TOKEN_GENERATE_PRE,
        UserEntityInterface $user = null,
        $data = null,
        ResultInterface $result = null
    )
    {
        $event = new RememberTokenEvent($this, $name, $user, $data, $result);
        if($this->request) {
            $event->setRequest($this->request);
        }
        if($this->response) {
            $event->setResponse($this->response);
        }

        return $event;
    }

}