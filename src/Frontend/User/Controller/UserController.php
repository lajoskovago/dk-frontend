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
use N3vrax\DkUser\FlashMessagesTrait;
use N3vrax\DkUser\Form\ChangePasswordForm;
use N3vrax\DkUser\Form\FormManager;
use N3vrax\DkUser\Result\UserOperationResult;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Form\Form;

class UserController extends AbstractActionController
{
    use FlashMessagesTrait;

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

        $identity = $this->authentication()->getIdentity();
        if(!$identity instanceof UserEntityInterface) {
            $user = $this->userService->findUser($identity->getId());
        }
        else {
            $user = $identity;
        }

        $userForm->bind($user);
        
        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            if(isset($data['userFormSubmit'])) {
                return $this->handleUpdateAccountInfo($request);
            }
            elseif(isset($data['changePasswordSubmit'])) {
                return $this->handleChangePassword($request);
            }
            else {
                $this->flashMessenger()->addError('Invalid form submitted');
                return new RedirectResponse($request->getUri(), 303);
            }
        }
        
        return new HtmlResponse($this->template()->render('app::account',
            ['userForm' => $userForm, 'changePasswordForm' => $changePasswordForm]));
    }

    protected function handleUpdateAccountInfo(ServerRequestInterface $request)
    {
        /** @var Form $form */
        $userForm = $this->formManager->get(UserForm::class);

        $data = $request->getParsedBody();
        $userForm->setData($data);

        if($userForm->isValid()) {
            /** @var UserEntityInterface $user */
            $user = $userForm->getData();
            /** @var UserOperationResult $result */
            $result = $this->userService->updateUser($user);
            if($result->isValid()) {
                $this->addSuccess('Account successfully updated', $this->flashMessenger());
                return new RedirectResponse($request->getUri(), 303);
            }
            else {
                $this->addError($result->getMessages(), $this->flashMessenger());
                return new RedirectResponse($request->getUri(), 303);
            }
        }
        else {
            $this->addError($this->getFormMessages($userForm->getMessages()), $this->flashMessenger());
            return new RedirectResponse($request->getUri(), 303);
        }
    }

    protected function handleChangePassword(ServerRequestInterface $request)
    {
        var_dump('update password');exit;
    }

    /**
     * @param array $formMessages
     * @return array
     */
    protected function getFormMessages(array $formMessages)
    {
        $messages = [];
        foreach ($formMessages as $message) {
            if(is_array($message)) {
                foreach ($message as $m) {
                    if(is_string($m)) {
                        $messages[] = $m;
                    }
                    elseif(is_array($m)) {
                        $messages = array_merge($messages, $this->getFormMessages($message));
                        break;
                    }
                }
            }
        }

        return $messages;
    }
}