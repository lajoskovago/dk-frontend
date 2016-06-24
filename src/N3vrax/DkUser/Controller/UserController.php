<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 10:11 PM
 */

namespace N3vrax\DkUser\Controller;

use N3vrax\DkBase\Controller\AbstractActionController;
use N3vrax\DkBase\Session\FlashMessenger;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Service\UserService;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Form\Form;

class UserController extends AbstractActionController
{
    /** @var  ModuleOptions */
    protected $options;

    /** @var  Form */
    protected $registerForm;

    /** @var  UserService */
    protected $userService;

    public function __construct(
        UserService $userService,
        ModuleOptions $options,
        Form $registerForm
    )
    {
        $this->userService = $userService;
        $this->registerForm = $registerForm;
        $this->options = $options;
    }

    public function indexAction()
    {

    }

    public function registerAction()
    {
        $request = $this->getRequest();
        
        /** @var FlashMessenger $messenger */
        $messenger = $this->flashMessenger();

        $form = $this->userService->getRegisterForm();
        $data = $messenger->getData('registerFormData') ?: [];
        $formMessages = $messenger->getData('registerFormMessages') ?: [];

        $form->setData($data);
        $form->setMessages($formMessages);

        if($request->getMethod() === 'POST')
        {
            $data = $request->getParsedBody();
            try {
                $user = $this->userService->register($data);
                if(!$user) {
                    $messages = $form->getMessages();
                    foreach ($messages as $element => $error) {
                        $messenger->addError(current($error));
                    }

                    $messenger->addData('registerFormData', $data);
                    $messenger->addData('registerFormMessages', $messages);

                    return new RedirectResponse($request->getUri(), 303);
                }
            }
            catch(\Exception $e) {
                error_log('User registration exception: ' . $e->getMessage(), E_USER_ERROR);

                $messenger->addError('Unexpected registration error. Please try again');
                $messenger->addData('registerFormData', $data);
                return new RedirectResponse($request->getUri(), 303);
            }

            if($this->options->isLoginAfterRegistration()) {
                //TODO: automatically login, directly or through redirection
            }
            else
            {
                $messenger->addSuccess('Confirmation email sent. Please check your inbox');
                return new RedirectResponse($this->urlHelper()->generate('login'));
            }
        }

        return new HtmlResponse(
            $this->template()->render('dk-user::register', ['form' => $form]));
    }

    public function forgotPasswordAction()
    {

    }

}