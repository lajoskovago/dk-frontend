<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 10:11 PM
 */

namespace N3vrax\DkUser\Controller;

use N3vrax\DkBase\Controller\AbstractActionController;
use N3vrax\DkBase\Controller\Plugin\FlashMessengerPlugin;
use N3vrax\DkBase\Session\FlashMessenger;
use N3vrax\DkMail\Service\MailServiceInterface;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\FlashMessagesTrait;
use N3vrax\DkUser\Options\ConfirmAccountOptions;
use N3vrax\DkUser\Options\PasswordRecoveryOptions;
use N3vrax\DkUser\Options\UserOptions;
use N3vrax\DkUser\Result\RegisterResult;
use N3vrax\DkUser\Result\ResultInterface;
use N3vrax\DkUser\Service\UserServiceInterface;
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

    /** @var  UserOptions */
    protected $options;

    /** @var  Form */
    protected $loginForm;

    /** @var  Form */
    protected $resetPasswordForm;

    /** @var  LoginAction */
    protected $loginAction;

    /** @var  Form */
    protected $registerForm;

    /** @var  UserServiceInterface */
    protected $userService;

    public function __construct(
        UserServiceInterface $userService,
        LoginAction $loginAction,
        UserOptions $options,
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
    }

    public function indexAction()
    {

    }

    public function confirmAccountAction()
    {
        if(!$this->options->getConfirmAccountOptions()->isEnableAccountConfirmation()) {
            $this->addError($this->options->getConfirmAccountOptions()->getMessage(
                ConfirmAccountOptions::MESSAGE_CONFIRM_ACCOUNT_DISABLED),
                $this->flashMessenger());

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = isset($params['email']) ? $params['email'] : '';
        $token = isset($params['token']) ? $params['token'] : '';

        /** @var ResultInterface $result */
        $result = $this->userService->confirmAccount($email, $token);
        if(!$result->isValid()) {
            $this->addError($result->getMessages(), $this->flashMessenger());
        }
        else {
            $this->addSuccess($result->getMessages(), $this->flashMessenger());
        }

        return new RedirectResponse($this->urlHelper()->generate('login'));
    }

    public function accountAction()
    {
        /** @var MailServiceInterface $mailService */
        $mailService = $this->sendMail();
        $mailService->setBody('This is a test');
        $mailService->getMessage()->setTo('n3vrax@gmail.com');
    }

    public function registerAction()
    {
        $request = $this->getRequest();

        if(!$this->options->getRegisterOptions()->isEnableRegistration()) {
            return new HtmlResponse(
                $this->template()->render('dk-user::register',
                    ['enableRegistration' => false]));
        }

        /** @var FlashMessengerPlugin $messenger */
        $messenger = $this->flashMessenger();

        $form = $this->userService->getRegisterForm();
        $data = $messenger->getData('registerFormData') ?: [];
        $formMessages = $messenger->getData('registerFormMessages') ?: [];

        $form->setData($data);
        $form->setMessages($formMessages);

        if($request->getMethod() === 'POST')
        {
            $data = $request->getParsedBody();

            /** @var RegisterResult $result */
            $result = $this->userService->register($data);
            if(!$result->isValid()) {
                $this->addError($result->getMessages(), $messenger);

                //as we use PRG forms, store form data in session for next page display
                $messenger->addData('registerFormData', $data);
                $messenger->addData('registerFormMessages', $form->getMessages());

                return new RedirectResponse($request->getUri(), 303);
            }
            else {
                $user = $result->getUser();
                if($this->options->getRegisterOptions()->isLoginAfterRegistration()) {
                    return $this->autoLoginUser($user, $data['password']);
                }
                else
                {
                    $this->addSuccess($result->getMessages(), $this->flashMessenger());
                    return new RedirectResponse($this->urlHelper()->generate('login'));
                }
            }
        }

        return new HtmlResponse(
            $this->template()->render('dk-user::register',
                [
                    'form' => $form,
                    'enableRegistration' => $this->options->getRegisterOptions()->isEnableRegistration()
                ]));
    }

    /**
     * Show the reset password form, validate data
     *
     * @return HtmlResponse|RedirectResponse
     */
    public function resetPasswordAction()
    {
        if(!$this->options->getPasswordRecoveryOptions()->isEnablePasswordRecovery()) {
            $this->addError($this->options->getPasswordRecoveryOptions()->getMessage(
                PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_DISABLED),
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