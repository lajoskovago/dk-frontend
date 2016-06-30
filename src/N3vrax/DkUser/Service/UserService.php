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
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Form;
use Zend\Math\Rand;

class UserService
{
    use EventManagerAwareTrait;

    const EVENT_REGISTER = 'register';
    const EVENT_REGISTER_POST = 'register.post';

    const EVENT_RESET_PASSWORD_REQUEST = 'reset_password_request';
    const EVENT_RESET_PASSWORD_REQUEST_POST = 'reset_password_request.post';

    const EVENT_RESET_PASSWORD = 'reset_password';
    const EVENT_RESET_PASSWORD_POST = 'reset_password_post';

    const EVENT_ACCOUNT_CONFIRM = 'account_confirm';
    const EVENT_ACCOUNT_CONFIRM_POST = 'account_confirm_post';

    const EVENT_CONFIRM_TOKEN = 'confirm_token';
    const EVENT_CONFIRM_TOKEN_POST = 'confirm_token_post';

    /** @var  UserMapperInterface */
    protected $userMapper;

    /** @var  ModuleOptions */
    protected $options;

    /** @var  RegisterOptions */
    protected $registerOptions;

    /** @var  Form */
    protected $registerForm;

    /** @var  Form */
    protected $resetPasswordForm;

    /** @var  UserEntityInterface */
    protected $userEntityPrototype;

    /** @var  PasswordInterface */
    protected $passwordService;


    public function __construct(
        UserMapperInterface $userMapper,
        ModuleOptions $options,
        RegisterOptions $registerOptions,
        Form $registerForm,
        Form $resetPasswordForm,
        UserEntityInterface $userEntityPrototype,
        PasswordInterface $passwordService
    )
    {
        $this->userMapper = $userMapper;
        $this->options = $options;
        $this->registerOptions = $registerOptions;
        $this->registerForm = $registerForm;
        $this->userEntityPrototype = $userEntityPrototype;
        $this->passwordService = $passwordService;
        $this->resetPasswordForm = $resetPasswordForm;

    }

    public function findUser($id)
    {
        return $this->userMapper->findUser($id);
    }

    public function findUserBy($field, $value)
    {
        return $this->userMapper->findUserBy($field, $value);
    }

    public function findAllUsers(array $filters = [])
    {
        return $this->userMapper->findAllUsers($filters);
    }

    public function findAllUsersPaginated(array $filters = [])
    {

    }

    public function saveUser($data)
    {
        return $this->userMapper->saveUser($data);
    }

    public function removeUser($id)
    {
        return $this->userMapper->removeUser($id);
    }
    
    public function getLastInsertValue()
    {
        return $this->userMapper->lastInsertValue();
    }

    public function confirmAccount($email, $token)
    {
        $errors = [];
        /** @var UserEntityInterface $user */
        $user = $this->findUserBy('email', $email);
        if($user) {
            $r = $this->userMapper->findConfirmToken($user->getId(), $token);
            if($r) {
                if($user->getStatus() == $this->options->getActiveUserStatus()) {
                    return $errors;
                }

                //if email-token pair found, change user status to active, only if user is on  the unconfirmed status or active
                if($user->getStatus() == $this->options->getNotConfirmedUserStatus()) {
                    $user->setStatus($this->options->getActiveUserStatus());

                    $this->getEventManager()->trigger(static::EVENT_ACCOUNT_CONFIRM, $this, ['user' => $user]);

                    $this->saveUser($user);

                    $this->getEventManager()->trigger(static::EVENT_ACCOUNT_CONFIRM_POST, $this, ['user' => $user]);
                }
                else {
                    $errors[] = $this->options->getMessage(DkUser::MESSAGE_CONFIRM_ACCOUNT_INVALID_ACCOUNT);
                }
            }
            else {
                $errors[] = $this->options->getMessage(DkUser::MESSAGE_CONFIRM_ACCOUNT_INVALID_TOKEN);
            }
        }
        else {
            $errors[] = $this->options->getMessage(DkUser::MESSAGE_CONFIRM_ACCOUNT_INVALID_EMAIL);
        }

        return $errors;
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
        $form = $this->registerForm;
        $form->bind($this->userEntityPrototype);
        $form->setData($data);

        if(!$form->isValid()) {
            return false;
        }

        /** @var UserEntityInterface $user */
        $user = $form->getData();

        $user->setPassword($this->passwordService->create($user->getPassword()));
        if($this->options->isEnableUserStatus()) {
            $user->setStatus($this->registerOptions->getDefaultUserStatus());
        }

        $this->getEventManager()->trigger(static::EVENT_REGISTER, $this,
            ['user' => $user, 'form' => $form]);

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

    
}