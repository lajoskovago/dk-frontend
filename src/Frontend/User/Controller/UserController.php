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
use N3vrax\DkUser\Form\FormManager;
use N3vrax\DkUser\Result\UserOperationResult;
use N3vrax\DkUser\Validator\NoRecordsExists;
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
        $form = $this->formManager->get(UserForm::class);

        /** @var UserEntity $identity */
        $identity = $this->authentication()->getIdentity();
        $form->bind($identity);

        $userFormData = $this->flashMessenger()->getData('userFormData') ?: [];
        $userFormMessages = $this->flashMessenger()->getData('userFormMessages') ?: [];

        $form->setData($userFormData);
        $form->setMessages($userFormMessages);
        
        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            //in case username is changed we need to check its uniqueness
            //but only in case username was actually changed from the previous one
            if($data['username'] !== $identity->getUsername()) {
                //consider we want to change username
                $form->getInputFilter()->get('username')
                    ->getValidatorChain()
                    ->attach(new NoRecordsExists([
                        'mapper' => $this->userService->getUserMapper(),
                        'key' => 'username',
                    ]));
            }
            $form->setData($data);

            $isValid = $form->isValid();

            $this->flashMessenger()->addData('userFormData', $data);
            $this->flashMessenger()->addData('userFormMessages', $form->getMessages());

            if($isValid) {
                /** @var UserEntityInterface $user */
                $user = $form->getData();

                /** @var UserOperationResult $result */
                $result = $this->userService->updateAccountInfo($user);

                if($result->isValid()) {
                    $this->addSuccess('Account successfully updated', $this->flashMessenger());
                    return new RedirectResponse($request->getUri());
                }
                else {
                    $this->addError($result->getMessages(), $this->flashMessenger());
                    return new RedirectResponse($request->getUri(), 303);
                }
            }
            else {
                $this->addError($this->getFormMessages($form->getMessages()), $this->flashMessenger());
                return new RedirectResponse($request->getUri(), 303);
            }
        }
        
        return new HtmlResponse($this->template()->render('app::account', ['form' => $form,]));
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