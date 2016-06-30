<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/30/2016
 * Time: 7:33 PM
 */

namespace N3vrax\DkMail\Result;

interface ResultInterface
{
    /**
     * Get error message when error occurs
     * @return string
     */
    public function getMessage();

    /**
     * Tells if the MailService that produced this result was properly sent
     * @return bool
     */
    public function isValid();

    /**
     * Tells if Result has an Exception
     * @return bool
     */
    public function hasException();

    /**
     * @return \Exception
     */
    public function getException();
}