<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/30/2016
 * Time: 8:52 PM
 */

namespace N3vrax\DkMail\Options;

use N3vrax\DkMail\Exception\InvalidArgumentException;
use N3vrax\DkMail\Service\MailServiceInterface;
use Zend\Stdlib\AbstractOptions;

class BodyOptions extends AbstractOptions
{
    protected $useTemplate = false;

    protected $content = '';

    protected $charset = MailServiceInterface::DEFAULT_CHARSET;
    
    /** @var  TemplateOptions */
    protected $template;

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
        $this->useTemplate = (bool) $useTemplate;
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

    /**
     * @return TemplateOptions
     */
    public function getTemplate()
    {
        if(!isset($this->template)) {
            $this->setTemplate([]);
        }
        return $this->template;
    }

    /**
     * @param TemplateOptions|array $template
     * @return BodyOptions
     */
    public function setTemplate($template)
    {
        if (is_array($template)) {
            $this->template = new TemplateOptions($template);
        }
        elseif ($template instanceof TemplateOptions) {
            $this->template = $template;
        }
        else {
            throw new InvalidArgumentException(sprintf(
                'Template should be an array or an %s object. %s provided.',
                TemplateOptions::class,
                is_object($template) ? get_class($template) : gettype($template)
            ));
        }
        return $this;
    }

    

}