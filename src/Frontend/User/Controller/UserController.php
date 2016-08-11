<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Frontend\User\Controller;

use Frontend\User\Entity\UserEntity;
use Frontend\User\Form\UserForm;
use Frontend\User\Service\UserServiceInterface;
use N3vrax\DkBase\Controller\AbstractActionController;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\FlashMessagesTrait;
use N3vrax\DkUser\Form\ChangePasswordForm;
use N3vrax\DkUser\Form\FormManager;
use N3vrax\DkUser\Result\UserOperationResult;
use N3vrax\DkUser\Validator\NoRecordsExists;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Form\Form;
use Zend\Form\FormInterface;

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

        //var_dump($this->authentication()->getIdentity());
        
        /** @var Form $form */
        $userForm = $this->formManager->get(UserForm::class);
        /** @var Form $changePasswordForm */
        $changePasswordForm = $this->formManager->get(ChangePasswordForm::class);

        /** @var UserEntity $identity */
        $identity = $this->authentication()->getIdentity();
        $userForm->bind($identity);

        $userFormData = $this->flashMessenger()->getData('userFormData') ?: [];
        $userFormMessages = $this->flashMessenger()->getData('userFormMessages') ?: [];

        $userForm->setData($userFormData);
        $userForm->setMessages($userFormMessages);
        
        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            if(isset($data['userFormSubmit'])) {
                //we add this flag into the session to be available next request, for PRG form
                $this->flashMessenger()->addData('submittedForm', 'userForm');
                return $this->handleUpdateAccountInfo($request);
            }
            elseif(isset($data['changePasswordSubmit'])) {
                $this->flashMessenger()->addData('submittedForm', 'changePasswordForm');
                return $this->handleChangePassword($request);
            }
            else {
                $this->flashMessenger()->addError('Invalid form submitted');
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        //read the previously flag indicating the form that was submitted
        //we'll make some UI decisions based on this is multiple forms are displayed on the same page
        $submittedForm = $this->flashMessenger()->getData('submittedForm') ?: 'none';
        
        return new HtmlResponse($this->template()->render('app::account',
            ['userForm' => $userForm, 'changePasswordForm' => $changePasswordForm, 'submittedForm' => $submittedForm]));
    }

    protected function handleUpdateAccountInfo(ServerRequestInterface $request)
    {
        /** @var UserEntity $identity */
        $identity = $this->authentication()->getIdentity();
        /** @var Form $form */
        $userForm = $this->formManager->get(UserForm::class);

        $data = $request->getParsedBody();
        //in case username is changed we need to check its uniqueness
        //but only in case username was actually changed from the previous one
        if($data['username'] !== $identity->getUsername()) {
            //consider we want to change username
            $userForm->getInputFilter()->get('username')
                ->getValidatorChain()
                ->attach(new NoRecordsExists([
                    'mapper' => $this->userService->getUserMapper(),
                    'key' => 'username',
                ]));
        }
        $userForm->setData($data);

        if($userForm->isValid()) {
            /** @var UserEntityInterface $user */
            $user = $userForm->getData();

            /** @var UserOperationResult $result */
            $result = $this->userService->updateAccountInfo($user);

            if($result->isValid()) {
                $this->addSuccess('Account successfully updated', $this->flashMessenger());
                return new RedirectResponse($request->getUri());
            }
            else {
                $this->flashMessenger()->addData('userFormData', $data);
                $this->flashMessenger()->addData('userFormMessages', $userForm->getMessages());

                $this->addError($result->getMessages(), $this->flashMessenger());
                return new RedirectResponse($request->getUri(), 303);
            }
        }
        else {
            $this->flashMessenger()->addData('userFormData', $data);
            $this->flashMessenger()->addData('userFormMessages', $userForm->getMessages());

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