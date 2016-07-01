<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/30/2016
 * Time: 9:02 PM
 */

namespace N3vrax\DkMail\Options;

use Zend\Mail\Transport\File;
use Zend\Mail\Transport\InMemory;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\TransportInterface;
use Zend\Stdlib\AbstractOptions;

class MailOptions extends AbstractOptions
{
    protected $adapterMap = [
        'sendmail' => [Sendmail::class],
        'smtp' => [Smtp::class],
        'in_memory' => [InMemory::class],
        'file' => [File::class],
    ];

    /** @var  TransportInterface|string */
    protected $mailAdapter = Sendmail::class;

    /** @var  MessageOptions */
    protected $messageOptions;

    protected $smtpOptions;

    protected $fileOptions;

    protected $mailListeners = [];

    /**
     * @return array
     */
    public function getAdapterMap()
    {
        return $this->adapterMap;
    }

    /**
     * @param array $adapterMap
     * @return MailOptions
     */
    public function setAdapterMap($adapterMap)
    {
        $this->adapterMap = $adapterMap;
        return $this;
    }

    /**
     * @return string|TransportInterface
     */
    public function getMailAdapter()
    {
        return $this->mailAdapter;
    }

    /**
     * @param string|TransportInterface $mailAdapter
     * @return MailOptions
     */
    public function setMailAdapter($mailAdapter)
    {
        $this->mailAdapter = $mailAdapter;
        return $this;
    }

    /**
     * @return MessageOptions
     */
    public function getMessageOptions()
    {
        return $this->messageOptions;
    }

    /**
     * @param MessageOptions $messageOptions
     * @return MailOptions
     */
    public function setMessageOptions($messageOptions)
    {
        $this->messageOptions = $messageOptions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSmtpOptions()
    {
        return $this->smtpOptions;
    }

    /**
     * @param mixed $smtpOptions
     * @return MailOptions
     */
    public function setSmtpOptions($smtpOptions)
    {
        $this->smtpOptions = $smtpOptions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFileOptions()
    {
        return $this->fileOptions;
    }

    /**
     * @param mixed $fileOptions
     * @return MailOptions
     */
    public function setFileOptions($fileOptions)
    {
        $this->fileOptions = $fileOptions;
        return $this;
    }

    /**
     * @return array
     */
    public function getMailListeners()
    {
        return $this->mailListeners;
    }

    /**
     * @param array $mailListeners
     * @return MailOptions
     */
    public function setMailListeners($mailListeners)
    {
        $this->mailListeners = $mailListeners;
        return $this;
    }
    
}