<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:04 PM
 */

namespace N3vrax\DkUser\Service;

use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Form;

class UserService
{
    use EventManagerAwareTrait;

    const EVENT_REGISTER = 'register';
    const EVENT_REGISTER_POST = 'register.post';

    /** @var  UserMapperInterface */
    protected $userMapper;

    /** @var  ModuleOptions */
    protected $options;

    /** @var  RegisterOptions */
    protected $registerOptions;

    /** @var  Form */
    protected $registerForm;

    /** @var  UserEntityInterface */
    protected $userEntityPrototype;

    /** @var  PasswordHashingInterface */
    protected $passwordService;


    public function __construct(
        UserMapperInterface $userMapper,
        ModuleOptions $options,
        RegisterOptions $registerOptions,
        Form $registerForm,
        UserEntityInterface $userEntityPrototype,
        PasswordHashingInterface $passwordService
    )
    {
        $this->userMapper = $userMapper;
        $this->options = $options;
        $this->registerOptions = $registerOptions;
        $this->registerForm = $registerForm;
        $this->userEntityPrototype = $userEntityPrototype;
        $this->passwordService = $passwordService;

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


        $this->getEventManager()->trigger(static::EVENT_REGISTER_POST, $this,
            ['user' => $user, 'form' => $form]);

        return $user;
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