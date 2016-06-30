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


}