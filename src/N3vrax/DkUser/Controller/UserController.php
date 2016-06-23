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
use Zend\Session\Container;

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

        /** @var Container $session */
        $session = $request->getAttribute(Container::class);

        $form = $this->userService->getRegisterForm();
        $data = [];
        $formErrors = [];
        if($session) {
            $data = $session->registerData ?: [];
            $formErrors = $session->registerFormMessages ?: [];

            unset($session->registerData);
            unset($session->registerFormMessages);
        }

        $form->setData($data);
        $form->setMessages($formErrors);

        if($request->getMethod() === 'POST')
        {
            $data = $request->getParsedBody();
            $user = $this->userService->register($data);
            
            if(!$user) {
                $messages = $form->getMessages();
                foreach ($messages as $element => $error) {
                    $messenger->addError(current($error));
                }

                if($session) {
                    $session->registerData = $data;
                    $session->registerFormMessages = $messages;
                }

                return new RedirectResponse($request->getUri(), 303);
            }

            $this->flashMessenger()->addInfo('Account successfully created');
            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        return new HtmlResponse(
            $this->template()->render('dk-user::register', ['form' => $form]));
    }

    public function forgotPasswordAction()
    {

    }

}