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

    /** @var bool  */
    protected $valid;

    /** @var string  */
    protected $message;

    /** @var \Exception  */
    protected $exception;

    /**
     * MailResult constructor.
     * @param bool $valid
     * @param string $message
     * @param null $exception
     */
    public function __construct($valid = true, $message = self::DEFAULT_MESSAGE, $exception = null)
    {
        $this->valid = $valid;
        $this->message = $message;
        $this->exception = $exception;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @return bool
     */
    public function hasException()
    {
        return $this->exception instanceof \Exception;
    }

    /**
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }
}