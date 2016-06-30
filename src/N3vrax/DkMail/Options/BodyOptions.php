<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/30/2016
 * Time: 8:52 PM
 */

namespace N3vrax\DkMail\Options;

use N3vrax\DkMail\Service\MailServiceInterface;
use Zend\Stdlib\AbstractOptions;

class BodyOptions extends AbstractOptions
{
    protected $useTemplate = false;

    protected $content = '';

    protected $charset = MailServiceInterface::DEFAULT_CHARSET;

    /**
     * @return boolean
     */
    public function isUseTemplate()
    {
        return $this->useTemplate;
    }

    /**
     * @param boolean $useTemplate
     * @return BodyOptions
     */
    public function setUseTemplate($useTemplate)
    {
        $this->useTemplate = $useTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return BodyOptions
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     * @return BodyOptions
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }


}