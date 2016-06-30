<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/30/2016
 * Time: 7:35 PM
 */

namespace N3vrax\DkMail\Result;

class MailResult implements ResultInterface
{
    const DEFAULT_MESSAGE = 'Success!';

    protected $valid;

    protected $message;

    protected $exception;

    public function __construct($valid = true, $message = self::DEFAULT_MESSAGE, $exception = null)
    {
        $this->valid = $valid;
        $this->message = $message;
        $this->exception = $exception;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function isValid()
    {
        return $this->valid;
    }

    public function hasException()
    {
        return $this->exception instanceof \Exception;
    }

    public function getException()
    {
        return $this->exception;
    }
}