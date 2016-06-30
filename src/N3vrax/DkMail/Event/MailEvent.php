<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/30/2016
 * Time: 7:50 PM
 */

namespace N3vrax\DkMail\Event;


use N3vrax\DkMail\Result\ResultInterface;
use N3vrax\DkMail\Service\MailServiceInterface;
use Zend\EventManager\Event;

class MailEvent extends Event
{
    const EVENT_MAIL_PRE_SEND = 'event.mail.pre.send';
    const EVENT_MAIL_POST_SEND = 'event.mail.post.send';
    const EVENT_MAIL_SEND_ERROR = 'event.mail.send.error';

    /** @var  MailServiceInterface */
    protected $mailService;

    /** @var  ResultInterface */
    protected $result;

    public function __construct(MailServiceInterface $mailService, $name = self::EVENT_MAIL_PRE_SEND)
    {
        parent::__construct($name);
        $this->mailService = $mailService;
    }

    /**
     * @return MailServiceInterface
     */
    public function getMailService()
    {
        return $this->mailService;
    }

    /**
     * @param MailServiceInterface $mailService
     * @return MailEvent
     */
    public function setMailService($mailService)
    {
        $this->mailService = $mailService;
        return $this;
    }

    /**
     * @return ResultInterface
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param ResultInterface $result
     * @return MailEvent
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    
}