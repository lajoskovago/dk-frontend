<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/30/2016
 * Time: 7:39 PM
 */

namespace N3vrax\DkMail\Service;

use N3vrax\DkMail\Event\MailEvent;
use N3vrax\DkMail\Event\MailListenerAwareInterface;
use N3vrax\DkMail\Event\MailListenerAwareTrait;
use N3vrax\DkMail\Exception\InvalidArgumentException;
use N3vrax\DkMail\Exception\MailException;
use N3vrax\DkMail\Result\MailResult;
use N3vrax\DkMail\Result\ResultInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mail\Exception\ExceptionInterface as ZendMailException;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class MailService implements MailServiceInterface, MailListenerAwareInterface
{
    use MailListenerAwareTrait;

    /** @var  Message */
    protected $message;

    /** @var  TemplateRendererInterface */
    protected $template;

    /** @var  TransportInterface */
    protected $transport;

    /** @var array  */
    protected $attachments = [];

    /** @var  ServerRequestInterface */
    protected $request;

    /** @var  ResponseInterface */
    protected $response;

    public function __construct(
        Message $message ,
        TransportInterface $transport,
        TemplateRendererInterface $template)
    {
        $this->message = $message;
        $this->transport = $transport;
        $this->template = $template;
    }

    public function send()
    {
        $result = new MailResult();
        try {
            $this->getEventManager()->triggerEvent($this->createMailEvent());

            //attach files before sending
            $this->attachFiles();

            $this->transport->send($this->message);

            $this->getEventManager()->triggerEvent($this->createMailEvent(MailEvent::EVENT_MAIL_POST_SEND, $result));
        }
        catch(\Exception $e) {
            $result = $this->createMailResultFromException($e);
            //trigger error event
            $this->getEventManager()->triggerEvent($this->createMailEvent(MailEvent::EVENT_MAIL_SEND_ERROR, $result));

            if(!$e instanceof ZendMailException) {
                throw new MailException('A non Zend\Mail exception occurred', $e->getCode(), $e);
            }

        }

        return $result;
    }

    protected function createMailEvent($name = MailEvent::EVENT_MAIL_PRE_SEND, ResultInterface $result = null)
    {
        $event = new MailEvent($this, $name);
        if($this->request) {
            $event->setRequest($this->request);
        }
        if($this->response) {
            $event->setResponse($this->response);
        }
        if(isset($result)) {
            $event->setResult($result);
        }
        return $event;
    }

    protected function createMailResultFromException(\Exception $e)
    {
        return new MailResult(false, $e->getMessage(), $e);
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setBody($body, $charset = null)
    {
        if(is_string($body)) {
            //create a mime\part and wrap it into a mime\message
            $mimePart = new MimePart($body);
            $mimePart->type = $body != strip_tags($body) ? Mime::TYPE_HTML : Mime::TYPE_TEXT;
            $mimePart->charset = $charset ?: self::DEFAULT_CHARSET;
            $body = new MimeMessage();
            $body->setParts([$mimePart]);
        }
        elseif($body instanceof MimePart) {
            if(isset($charset)) {
                $body->charset = $charset;
            }

            $mimeMessage = new MimeMessage();
            $mimeMessage->setParts([$body]);
            $body = $mimeMessage;
        }

        //if the body is not a string or mime message at this point, it is not a valid argument
        if(!is_string($body) && !$body instanceof MimeMessage) {
            throw new InvalidArgumentException(sprintf(
                'Provided body is not valid. It should be one of "%s". %s provided',
                implode('", "', ['string', 'Zend\Mime\Part', 'Zend\Mime\Message']),
                is_object($body) ? get_class($body) : gettype($body)
            ));
        }

        // The headers Content-type and Content-transfer-encoding are duplicated every time the body is set.
        // Removing them before setting the body prevents this error
        $this->message->getHeaders()->removeHeader('content-type');
        $this->message->getHeaders()->removeHeader('content-transfer-encoding');
        $this->message->setBody($body);
        return $this;
    }

    public function setTemplate($template, array $params = [])
    {
        $this->setBody($this->template->render($template, $params));
        return $this;
    }

    public function setSubject($subject)
    {
        $this->message->setSubject($subject);
        return $this;
    }

    public function addAttachment($path, $filename = null)
    {
        if(isset($filename)) {
            $this->attachments[$filename] = $path;
        }
        else {
            $this->attachments[] = $path;
        }
        return $this;
    }

    public function addAttachments(array $paths)
    {
        return $this->setAttachments(array_merge($this->attachments, $paths));
    }

    public function getAttachments()
    {
        return $this->attachments;
    }

    public function setAttachments(array $paths)
    {
        $this->attachments = $paths;
        return $this;
    }

    protected function attachFiles()
    {
        if(count($this->attachments) === 0) {
            return;
        }

        $mimeMessage = $this->message->getBody();
        if(is_string($mimeMessage)) {
            $originalBodyPart = new MimePart($mimeMessage);
            $originalBodyPart->type = $mimeMessage != strip_tags($mimeMessage)
                ? Mime::TYPE_HTML
                : Mime::TYPE_TEXT;

            $this->setBody($originalBodyPart);
            $mimeMessage = $this->message->getBody();
        }

        $oldParts = $mimeMessage->getParts();

        //generate a new Part for each attachment
        $attachmentParts = [];
        $info = new \finfo(FILEINFO_MIME_TYPE);
        foreach ($this->attachments as $key => $attachment) {
            if(!is_file($attachment)) {
                continue;
            }

            $basename = is_string($key) ? $key : basename($attachment);

            $part = new MimePart(fopen($attachment, 'r'));
            $part->id = $basename;
            $part->filename = $basename;
            $part->type = $info->file($attachment);
            $part->encoding = Mime::ENCODING_BASE64;
            $part->disposition = Mime::DISPOSITION_ATTACHMENT;
            $attachmentParts[] = $part;
        }

        $body = new MimeMessage();
        $body->setParts(array_merge($oldParts, $attachmentParts));
        $this->message->setBody($body);
    }

    /**
     * @return TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param TransportInterface $transport
     * @return MailService
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
        return $this;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param ServerRequestInterface $request
     * @return MailService
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     * @return MailService
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }


}