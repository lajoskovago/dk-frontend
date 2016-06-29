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
use N3vrax\DkUser\DkUser;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\FlashMessagesTrait;
use N3vrax\DkUser\Options\ModuleOptions;
use N3vrax\DkUser\Options\RegisterOptions;
use N3vrax\DkUser\Service\UserService;
use N3vrax\DkWebAuthentication\Action\LoginAction;
use N3vrax\DkWebAuthentication\Event\AuthenticationEvent;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
use Zend\Form\Element\Csrf;
use Zend\Form\Form;

class UserController extends AbstractActionController
{
    use FlashMessagesTrait;

    /** @var  ModuleOptions */
    protected $options;

    /** @var  RegisterOptions */
    protected $registerOptions;

    /** @var  Form */
    protected $loginForm;

    /** @var  Form */
    protected $resetPasswordForm;

    /** @var  LoginAction */
    protected $loginAction;

    /** @var  Form */
    protected $registerForm;

    /** @var  UserService */
    protected $userService;

    public function __construct(
        UserService $userService,
        LoginAction $loginAction,
        ModuleOptions $options,
        RegisterOptions $registerOptions,
        Form $loginForm,
        Form $registerForm,
        Form $resetPasswordForm
    )
    {
        $this->userService = $userService;
        $this->registerForm = $registerForm;
        $this->options = $options;
        $this->loginAction = $loginAction;
        $this->loginForm = $loginForm;
        $this->resetPasswordForm = $resetPasswordForm;
        $this->registerOptions = $registerOptions;
    }

    public function indexAction()
    {

    }

    public function confirmAccountAction()
    {
        if(!$this->registerOptions->isEnableAccountConfirmation()) {
            $this->addError($this->options->getMessage(
                DkUser::MESSAGE_CONFIRM_ACCOUNT_DISABLED),
                $this->flashMessenger());

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();
        $email = isset($params['email']) ? $params['email'] : '';
        $token = isset($params['token']) ? $params['token'] : '';

        if(empty($email) || empty($token)) {
            $this->addError($this->options->getMessage(
                DkUser::MESSAGE_CONFIRM_ACCOUNT_MISSING_PARAMS),
                $this->flashMessenger());

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        try {
            $errors = $this->userService->confirmAccount($email, $token);

            if(!empty($errors)) {
                $this->addError($errors, $this->flashMessenger());
                return new RedirectResponse($this->urlHelper()->generate('login'));
            }

            $this->addSuccess($this->options->getMessage(
                DkUser::MESSAGE_CONFIRM_ACCOUNT_SUCCESS),
                $this->flashMessenger());

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }
        catch(\Exception $e) {
            error_log('Account confirmation exception: ' . $e->getMessage(), E_USER_ERROR);

            $this->addError($this->options->getMessage(
                DkUser::MESSAGE_CONFIRM_ACCOUNT_ERROR),
                $this->flashMessenger());

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }
    }

    public function accountAction()
    {
        
    }

    public function registerAction()
    {
        $request = $this->getRequest();

        if(!$this->options->isEnableRegistration()) {
            return new HtmlResponse(
                $this->template()->render('dk-user::register',
                    ['enableRegistration' => false]));
        }

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
                return $this->autoLoginUser($user, $data['password']);
            }
            else
            {
                $messenger->addSuccess('Account created successfully');
                return new RedirectResponse($this->urlHelper()->generate('login'));
            }
        }

        return new HtmlResponse(
            $this->template()->render('dk-user::register',
                ['form' => $form, 'enableRegistration' => $this->options->isEnableRegistration()]));
    }

    /**
     * Show the reset password form, validate data
     *
     * @return HtmlResponse|RedirectResponse
     */
    public function resetPasswordAction()
    {
        if(!$this->options->isEnablePasswordRecovery()) {
            $this->addError($this->options->getMessage(
                DkUser::MESSAGE_RESET_PASSWORD_DISABLED),
                $this->flashMessenger());

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();
        $email = isset($params['email']) ? $params['email'] : '';
        $token = isset($params['token']) ? $params['token'] : '';

        if(empty($email) || empty($token)) {
            $this->addError($this->options->getMessage(
                DkUser::MESSAGE_RESET_PASSWORD_MISSING_PARAMS),
                $this->flashMessenger());

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        $form = $this->resetPasswordForm;

        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            try {
                $errors = $this->userService->resetPassword($email, $token, $data);

                if(!empty($errors)) {
                    $this->addError($errors, $this->flashMessenger());
                    return new RedirectResponse($request->getUri(), 303);
                }

                $this->addSuccess($this->options->getMessage(
                    DkUser::MESSAGE_RESET_PASSWORD_SUCCESS),
                    $this->flashMessenger());

                return new RedirectResponse($this->urlHelper()->generate('login'));
            }
            catch(\Exception $e) {
                error_log('Account reset password exception: ' . $e->getMessage(), E_USER_ERROR);

                $this->addError($this->options->getMessage(
                    DkUser::MESSAGE_RESET_PASSWORD_ERROR),
                    $this->flashMessenger());

                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse($this->template()->render('dk-user::reset-password', ['form' => $form]));
    }

    /**
     * @return HtmlResponse|RedirectResponse
     */
    public function forgotPasswordAction()
    {
        if(!$this->options->isEnablePasswordRecovery()) {
            $this->addError($this->options->getMessage(
                DkUser::MESSAGE_RESET_PASSWORD_DISABLED),
                $this->flashMessenger());

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        $request = $this->getRequest();

        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $email = isset($data['email']) ? $data['email'] : '';

            if(empty($email)) {
                $this->addError($this->options->getMessage(
                    DkUser::MESSAGE_FORGOT_PASSWORD_MISSING_EMAIL),
                    $this->flashMessenger());

                return new RedirectResponse($request->getUri(), 303);
            }

            try {
                $this->userService->resetPasswordRequest($email);
                //we don't check if email was found or not, we don't want to give this info as error

                $this->addInfo($this->options->getMessage(
                    DkUser::MESSAGE_FORGOT_PASSWORD_SUCCESS),
                    $this->flashMessenger());

                return new RedirectResponse($this->urlHelper()->generate('login'));
            }
            catch(\Exception $e) {
                error_log('User reset password request exception: ' . $e->getMessage(), E_USER_ERROR);

                $this->addError($this->options->getMessage(
                    DkUser::MESSAGE_FORGOT_PASSWORD_ERROR),
                    $this->flashMessenger());

                return new RedirectResponse($request->getUri(), 303);
            }

        }

        return new HtmlResponse($this->template()->render('dk-user::forgot-password'));
    }

    /**
     * Force an auth event using the LoginAction to automatically login the user after registration
     *
     * @param UserEntityInterface $user
     * @param $password
     * @return mixed
     */
    protected function autoLoginUser(UserEntityInterface $user, $password)
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        
        $form = $this->loginForm;
        $form->init();
        $csrf = ['name' => '', 'value' => ''];
        foreach ($form->getElements() as $element) {
            if($element instanceof Csrf) {
                $csrf['name'] = $element->getName();
                $csrf['value'] = $element->getValue();
            }
        }

        $loginData = [
            'identity' => $user->getEmail(),
            'password' => $password,
            'remember' => 'no',
        ];

        if(!empty($csrf['name'])) {
            $loginData[$csrf['name']] = $csrf['value'];
        }

        $form->setData($loginData);

        $form->isValid();

        $request = $request->withParsedBody($form->getData())
            ->withUri(new Uri($this->urlHelper()->generate('login')));

        return $this->loginAction->triggerEvent(
            AuthenticationEvent::EVENT_AUTHENTICATE,
            $request->getParsedBody(),
            $request,
            $response);
    }

}