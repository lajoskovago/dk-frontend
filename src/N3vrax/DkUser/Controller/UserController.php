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
use Psr\Http\Message\ServerRequestInterface;
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

    /** @var  LoginAction */
    protected $loginAction;

    /** @var  UserServiceInterface */
    protected $userService;

    public function __construct(
        UserServiceInterface $userService,
        LoginAction $loginAction,
        UserOptions $options,
        Form $loginForm
    )
    {
        $this->userService = $userService;
        $this->options = $options;
        $this->loginAction = $loginAction;
        $this->loginForm = $loginForm;
    }

    public function confirmAccountAction()
    {
        $this->userService->setRequest($this->getRequest());
        $this->userService->setResponse($this->getResponse());

        if (!$this->options->getConfirmAccountOptions()->isEnableAccountConfirmation()) {
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
        if (!$result->isValid()) {
            $this->addError($result->getMessages(), $this->flashMessenger());
        } else {
            $this->addSuccess($result->getMessages(), $this->flashMessenger());
        }

        return new RedirectResponse($this->urlHelper()->generate('login'));
    }

    public function registerAction()
    {
        $this->userService->setRequest($this->getRequest());
        $this->userService->setResponse($this->getResponse());

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
        $this->userService->setRequest($this->getRequest());
        $this->userService->setResponse($this->getResponse());

        /** @var FlashMessengerPlugin $messenger */
        $messenger = $this->flashMessenger();

        if(!$this->options->getPasswordRecoveryOptions()->isEnablePasswordRecovery()) {
            $this->addError($this->options->getPasswordRecoveryOptions()->getMessage(
                PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_DISABLED),
                $messenger);

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = isset($params['email']) ? $params['email'] : '';
        $token = isset($params['token']) ? $params['token'] : '';

        $data = $messenger->getData('resetPasswordFormData') ?: [];
        $formMessages = $messenger->getData('resetPasswordFormMessages') ?: [];

        $form = $this->userService->getResetPasswordForm();
        $form->setData($data);
        $form->setMessages($formMessages);

        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            /** @var ResultInterface $result */
            $result = $this->userService->resetPassword($email, $token, $data);
            if(!$result->isValid()) {
                $this->addError($result->getMessages(), $messenger);

                $messenger->addData('resetPasswordFormData', $data);
                $messenger->addData('resetPasswordFormMessages', $form->getMessages());

                return new RedirectResponse($request->getUri(), 303);
            }
            else {
                $this->addSuccess($result->getMessages(), $messenger);
            }

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        return new HtmlResponse($this->template()->render('dk-user::reset-password', ['form' => $form]));
    }

    /**
     * @return HtmlResponse|RedirectResponse
     */
    public function forgotPasswordAction()
    {
        $this->userService->setRequest($this->getRequest());
        $this->userService->setResponse($this->getResponse());

        if(!$this->options->getPasswordRecoveryOptions()->isEnablePasswordRecovery()) {
            $this->addError($this->options->getPasswordRecoveryOptions()->getMessage(
                PasswordRecoveryOptions::MESSAGE_RESET_PASSWORD_DISABLED),
                $this->flashMessenger());

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        $request = $this->getRequest();

        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $email = isset($data['email']) ? $data['email'] : '';

            /** @var ResultInterface $result */
            $result = $this->userService->resetPasswordRequest($email);
            if($result->isValid()) {
                $this->addInfo($result->getMessages(), $this->flashMessenger());
            }
            else {
                $this->addError($result->getMessages(), $this->flashMessenger());
                return new RedirectResponse($request->getUri(), 303);
            }

            return new RedirectResponse($this->urlHelper()->generate('login'));

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
        /** @var ServerRequestInterface $request */
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