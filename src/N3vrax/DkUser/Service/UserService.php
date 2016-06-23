<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:04 PM
 */

namespace N3vrax\DkUser\Service;

use N3vrax\DkUser\Mapper\UserMapperInterface;
use N3vrax\DkUser\Options\ModuleOptions;
use Zend\Form\Form;

class UserService
{
    /** @var  UserMapperInterface */
    protected $userMapper;

    /** @var  ModuleOptions */
    protected $options;

    /** @var  Form */
    protected $registerForm;

    /** @var  object */
    protected $userEntityPrototype;


    public function __construct(
        UserMapperInterface $userMapper,
        ModuleOptions $options,
        Form $registerForm,
        $userEntityPrototype
    )
    {
        $this->userMapper = $userMapper;
        $this->options = $options;
        $this->registerForm = $registerForm;
        $this->userEntityPrototype = $userEntityPrototype;

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

    public function register($data)
    {
        $form = $this->registerForm;
        $form->bind($this->userEntityPrototype);
        $form->setData($data);

        if(!$form->isValid()) {
            return false;
        }

        $user = $form->getData();

        //TODO: hash password before inserting

        //TODO: trigger pre and post register events
        $this->saveUser($user);

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
     * @return object
     */
    public function getUserEntityPrototype()
    {
        return $this->userEntityPrototype;
    }

    /**
     * @param object $userEntityPrototype
     * @return UserService
     */
    public function setUserEntityPrototype($userEntityPrototype)
    {
        $this->userEntityPrototype = $userEntityPrototype;
        return $this;
    }

    
}