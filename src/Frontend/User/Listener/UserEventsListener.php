<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/10/2016
 * Time: 4:48 PM
 */

namespace Frontend\User\Listener;

use N3vrax\DkMail\Service\MailServiceInterface;
use N3vrax\DkUser\Entity\UserEntityInterface;
use N3vrax\DkUser\Event\ConfirmTokenGenerateEvent;
use N3vrax\DkUser\Event\PasswordResetEvent;
use N3vrax\DkUser\Event\RegisterEvent;
use N3vrax\DkUser\Options\UserOptions;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;

class UserEventsListener extends AbstractListenerAggregate
{
    /** @var MailServiceInterface  */
    protected $mailService;

    /** @var  string */
    protected $confirmToken;

    /** @var  mixed */
    protected $resetToken;

    /** @var  ServerUrlHelper */
    protected $serverUrlHelper;

    /** @var  UrlHelper */
    protected $urlHelper;

    /**
     * RegisterListener constructor.
     * @param MailServiceInterface $mailService
     * @param ServerUrlHelper $serverUrlHelper
     * @param UrlHelper $urlHelper
     */
    public function __construct(
        MailServiceInterface $mailService,
        ServerUrlHelper $serverUrlHelper,
        UrlHelper $urlHelper
    )
    {
        $this->mailService = $mailService;
        $this->serverUrlHelper = $serverUrlHelper;
        $this->urlHelper = $urlHelper;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(RegisterEvent::EVENT_REGISTER_PRE, [$this, 'onPreRegister'], $priority);
        $this->listeners[] = $events->attach(RegisterEvent::EVENT_REGISTER_POST, [$this, 'onPostRegister'], $priority);
        $this->listeners[] = $events->attach(RegisterEvent::EVENT_REGISTER_ERROR, [$this, 'onRegisterError'], $priority);

        $this->listeners[] = $events->attach(ConfirmTokenGenerateEvent::EVENT_GENERATE_CONFIRM_TOKEN_POST,
            [$this, 'onConfirmTokenGenerated'], $priority);

        $this->listeners[] = $events->attach(PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_POST,
            [$this, 'onResetPasswordTokenGenerated'], $priority);
    }

    public function onConfirmTokenGenerated(ConfirmTokenGenerateEvent $e)
    {
        $data = $e->getData();
        $this->confirmToken = $data->token;
    }

    public function onResetPasswordTokenGenerated(PasswordResetEvent $e)
    {
        $data = $e->getData();
        $this->resetToken = $data->token;

        if(!$this->resetToken) {
            return;
        }

        $user = $e->getUser();

        //send an email with the link to the reset page
        $resetPasswordUri = $this->urlHelper->generate('user', ['action' => 'reset-password']);
        $query = ['email' => $user->getEmail(), 'token' => $this->resetToken];
        $resetPasswordUri .= '?' . http_build_query($query);

        $message = $this->mailService->getMessage();
        $message->setTo($user->getEmail());
        $message->setFrom('pgt.dot.work@gmail.com', 'Dotkernel Team');
        $message->setSubject('DotKernel Password Reset');

        $message->setBody("You have requested an account password reset".
            "\nIf you didn't make this request, please ignore this email \n".
            "In order to reset your password, click the link bellow\n\n".
            $this->serverUrlHelper->generate($resetPasswordUri). "\n\n".
            "Please note this link will expired within an hour. Do not share this information with anyone!"
        );

        $this->mailService->send();

    }

    public function onPreRegister(RegisterEvent $e)
    {

    }

    public function onPostRegister(RegisterEvent $e)
    {
        //if we don't have a confirm token, just return
        if(!$this->confirmToken) {
            return;
        }

        //send an email if account confirmation is needed
        $userService = $e->getUserService();
        /** @var UserOptions $options */
        $options = $userService->getOptions();
        /** @var UserEntityInterface $user */
        $user = $e->getUserEntity();

        $confirmAccountUri = $this->urlHelper->generate('user', ['action' => 'confirm-account']);
        $query = ['email' => $user->getEmail(), 'token' => $this->confirmToken];
        $confirmAccountUri .= '?' . http_build_query($query);

        if($options->getConfirmAccountOptions()->isEnableAccountConfirmation()) {
            $message = $this->mailService->getMessage();
            $message->setTo($user->getEmail());
            $message->setFrom('pgt.dot.work@gmail.com', 'Dotkernel Team');
            $message->setSubject('DotKernel Account confirmation');
            
            $message->setBody("Welcome to Dotkernel 3. Thanks you for registering with us.".
                "\nClick the link below to confirm your account \n\n".
                $this->serverUrlHelper->generate($confirmAccountUri)
            );

            $this->mailService->send();
        }
    }

    public function onRegisterError(RegisterEvent $e)
    {

    }
}