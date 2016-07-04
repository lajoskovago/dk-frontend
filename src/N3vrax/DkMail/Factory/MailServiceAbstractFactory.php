<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/30/2016
 * Time: 8:49 PM
 */

namespace N3vrax\DkMail\Factory;

use Interop\Container\ContainerInterface;
use N3vrax\DkMail\Event\MailListenerAwareInterface;
use N3vrax\DkMail\Event\MailListenerInterface;
use N3vrax\DkMail\Exception\InvalidArgumentException;
use N3vrax\DkMail\Options\MailOptions;
use N3vrax\DkMail\Service\MailService;
use Zend\EventManager\EventManagerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Mail\Message;
use Zend\Mail\Transport\File;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\TransportInterface;

class MailServiceAbstractFactory extends AbstractMailFactory
{
    const SPECIFIC_PART = 'mailservice';

    /** @var  MailOptions */
    protected $mailOptions;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $specificServiceName = explode('.', $requestedName)[2];
        $this->mailOptions = $container->get(sprintf(
            '%s.%s.%s', self::DKMAIL_PART, MailOptionsAbstractFactory::SPECIFIC_PART, $specificServiceName
        ));

        $template = $container->get(TemplateRendererInterface::class);

        $message = $this->createMessage();
        $transport = $this->createTransport($container);

        $mailService = new MailService($message, $transport, $template);

        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : null;

        if($eventManager)
            $mailService->setEventManager($eventManager);

        //set subject
        $mailService->setSubject($this->mailOptions->getMessageOptions()->getSubject());

        //set body, either by using a template or a raw body
        $body = $this->mailOptions->getMessageOptions()->getBody();
        if($body->isUseTemplate()) {
            $mailService->setTemplate($body->getTemplate()->getName(), $body->getTemplate()->getParams());
        }
        else {
            $mailService->setBody($body->getContent(), $body->getCharset());
        }

        //attach files
        $files = $this->mailOptions->getMessageOptions()->getAttachments()->getFiles();
        $mailService->addAttachments($files);

        //attach files from dir
        $dir = $this->mailOptions->getMessageOptions()->getAttachments()->getDir();
        if($dir['iterate'] === true && is_string($dir['path']) && is_dir('path')) {
            $files = $dir['recursive'] === true
                ? new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        $dir['path'],
                        \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST)
                : new \DirectoryIterator($dir['path']);

            foreach ($files as $fileInfo) {
                if($fileInfo->isDir()) {
                    continue;
                }

                $mailService->addAttachment($fileInfo->getPathname());
            }
        }

        $this->attachMailListeners($mailService, $container);
        return $mailService;

    }
    
    protected function createMessage()
    {
        $options = $this->mailOptions->getMessageOptions();
        $message = new Message();

        $from = $options->getFrom();
        if(!empty($from)) {
            $message->setFrom($from, $options->getFromName());
        }

        $replyTo = $options->getReplyTo();
        if(!empty($replyTo)) {
            $message->setReplyTo($replyTo, $options->getReplyToName());
        }

        $to = $options->getTo();
        if(!empty($to)) {
            $message->setTo($to);
        }

        $cc = $options->getCc();
        if(!empty($cc)) {
            $message->setCc($cc);
        }

        $bcc = $options->getBcc();
        if(!empty($bcc)) {
            $message->setBcc($bcc);
        }

        return $message;
    }

    protected function createTransport(ContainerInterface $container)
    {
        $adapter = $this->mailOptions->getTransport();
        if($adapter instanceof TransportInterface) {
            return $this->setupTransportConfig($adapter);
        }

        //check is adapter is a service
        if(is_string($adapter) && $container->has($adapter)) {
            $transport = $container->get($adapter);
            if($transport instanceof TransportInterface) {
                return $this->setupTransportConfig($transport);
            }
            else {
                throw new InvalidArgumentException(
                    'Provided mail_adapter service does not return a ' . TransportInterface::class . ' instance'
                );
            }
        }

        //check is the adapter is one of Zend's default adapters
        if(is_string($adapter) && is_subclass_of($adapter, TransportInterface::class)) {
            return $this->setupTransportConfig(new $adapter);
        }

        //the adapter is not valid - throw exception
        throw new InvalidArgumentException(
            sprintf(
                'mail_adapter must be an instance of %s or string, "%s" provided',
                TransportInterface::class,
                is_object($adapter) ? get_class($adapter) : gettype($adapter)
            )
        );
    }

    protected function setupTransportConfig(TransportInterface $transport)
    {
        if($transport instanceof Smtp) {
            $transport->setOptions($this->mailOptions->getSmtpOptions());
        }
        elseif($transport instanceof File) {
            $transport->setOptions($this->mailOptions->getFileOptions());
        }

        return $transport;
    }

    protected function attachMailListeners(MailListenerAwareInterface $service, ContainerInterface $container)
    {
        $listeners = $this->mailOptions->getMailListeners();
        foreach ($listeners as $listener) {
            if(is_string($listener) && $container->has($listener)) {
                $listener = $container->get($listener);
            }
            elseif(is_string($listener) && class_exists($listener)) {
                $listener = new $listener;
            }

            if(!$listener instanceof MailListenerInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Provided mail listener of type "%s" is not valid. Expected string or %s',
                    is_object($listener) ? get_class($listener) : gettype($listener),
                    MailListenerInterface::class
                ));
            }
            
            $service->attachMailListener($listener);
        }
    }
}