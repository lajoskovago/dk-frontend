<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/30/2016
 * Time: 7:13 PM
 */

namespace N3vrax\DkMail\Service;

use N3vrax\DkMail\Result\ResultInterface;
use Zend\Mail\Message;
use Zend\Mime\Part;

interface MailServiceInterface
{
    const DEFAULT_CHARSET = 'utf-8';

    /**
     * @return ResultInterface
     */
    public function send();

    /**
     * @return Message
     */
    public function getMessage();

    /**
     * @param string|Part|\Zend\Mime\Message $body
     * @param string $charset
     * @return mixed
     */
    public function setBody($body, $charset = null);

    /**
     * @param string $template
     * @param array $params
     * @return mixed
     */
    public function setTemplate($template, array $params = []);

    /**
     * @param string $subject
     * @return mixed
     */
    public function setSubject($subject);

    /**
     * @param string $path
     * @param string|null $filename
     * @return mixed
     */
    public function addAttachment($path, $filename = null);

    /**
     * @param array $paths
     * @return mixed
     */
    public function addAttachments(array $paths);

    /**
     * @return array
     */
    public function getAttachments();

    /**
     * @param array $paths
     * @return mixed
     */
    public function setAttachments(array $paths);
}