<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Frontend\User\Controller;

use Frontend\User\Form\UserForm;
use Frontend\User\Service\UserServiceInterface;
use N3vrax\DkBase\Controller\AbstractActionController;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Form\ChangePasswordForm;
use N3vrax\DkUser\Form\FormManager;
use N3vrax\DkUser\Result\UserOperationResult;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Form\Form;

class UserController extends AbstractActionController
{
    /** @var  FormManager */
    protected $formManager;

    /** @var  UserServiceInterface */
    protected $userService;

    public function __construct(UserServiceInterface $userService, FormManager $formManager)
    {
        $this->userService = $userService;
        $this->formManager = $formManager;
    }

    public function accountAction()
    {
        $request = $this->getRequest();
        
        /** @var Form $form */
        $userForm = $this->formManager->get(UserForm::class);
        /** @var Form $changePasswordForm */
        $changePasswordForm = $this->formManager->get(ChangePasswordForm::class);
        
        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $userForm->bind($this->userService->getUserEntityPrototype());
            $userForm->setData($data);

            if($userForm->isValid()) {
                /** @var UserEntityInterface $user */
                $user = $userForm->getData();
                /** @var UserOperationResult $result */
                $result = $this->userService->updateUser($user);
                if($result->isValid()) {

                }
                else {

                }
            }
            else {

            }
        }
        
        return new HtmlResponse($this->template()->render('app::account',
            ['userForm' => $userForm, 'changePasswordForm' => $changePasswordForm]));
    }
}