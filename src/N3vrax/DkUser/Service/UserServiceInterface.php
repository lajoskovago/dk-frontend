<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/5/2016
 * Time: 11:08 PM
 */
namespace N3vrax\DkUser\Service;

use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Options\UserOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Form\Form;

interface UserServiceInterface
{
    /**
     * Find user by its id
     *
     * @param $id
     * @return mixed
     */
    public function findUser($id);

    /**
     * Get a user entity by some given field and value
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public function findUserBy($field, $value);

    /**
     * Gets all users from the backend
     *
     * @param array $filters
     * @return mixed
     */
    public function findAllUsers(array $filters = []);

    /**
     * Return a paginated list of users based on some filters
     *
     * @param array $filters
     */
    public function findAllUsersPaginated(array $filters = []);

    /**
     * Save user is working as in create/update user, based on the presence of user id in the data
     *
     * @param $data
     * @return mixed
     */
    public function saveUser($data);

    /**
     * Remove an user based on its id
     *
     * @param $id
     * @return mixed
     */
    public function removeUser($id);

    /**
     * Get the last id generated
     *
     * @return mixed
     */
    public function getLastInsertValue();

    /**
     * Change user status from unconfirmed to active based on an email and valid confirmation token
     *
     * @param $email
     * @param $token
     * @return array
     */
    public function confirmAccount($email, $token);

    /**
     * Based on a user email, generate a token and store a hash of it with and expiration time
     * trigger a specific event, so mail service can send an email based on it
     *
     * @param $email
     * @return bool
     */
    public function resetPasswordRequest($email);

    /**
     * @param $email
     * @param $token
     * @param $data
     * @return array
     */
    public function resetPassword($email, $token, $data);

    /**
     * Store a new user into the db, after it validates the data
     * trigger register events
     *
     * @param $data
     * @return bool|UserEntityInterface
     */
    public function register($data);

    /**
     * @param mixed $userId
     * @return mixed
     */
    public function generateRememberToken($userId);

    /**
     * Validates the remember me cookie data
     *
     * @param $selector
     * @param $token
     * @return mixed
     */
    public function checkRememberToken($selector, $token);

    /**
     * Removes all remember tokens for a given user
     *
     * @param $userId
     * @return mixed
     */
    public function removeRememberToken($userId);

    /**
     * @return Form
     */
    public function getRegisterForm();

    /**
     * @param Form $registerForm
     * @return UserService
     */
    public function setRegisterForm(Form $registerForm);

    /**
     * @return Form
     */
    public function getResetPasswordForm();

    /**
     * @param Form $form
     * @return mixed
     */
    public function setResetPasswordForm(Form $form);

    /**
     * @return UserEntityInterface
     */
    public function getUserEntityPrototype();

    /**
     * @param UserEntityInterface $userEntityPrototype
     * @return UserService
     */
    public function setUserEntityPrototype($userEntityPrototype);

    /**
     * @return mixed
     */
    public function getOptions();

    /**
     * @param UserOptions $options
     * @return mixed
     */
    public function setOptions(UserOptions $options);

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function setRequest(ServerRequestInterface $request);

    /**
     * @return ServerRequestInterface
     */
    public function getRequest();

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function setResponse(ResponseInterface $response);

    /**
     * @return ResponseInterface
     */
    public function getResponse();
}