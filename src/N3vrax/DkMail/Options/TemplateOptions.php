<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/1/2016
 * Time: 8:53 PM
 */

namespace N3vrax\DkMail\Options;

class TemplateOptions
{
    /** @var array  */
    protected $params = [];

    /** @var  string */
    protected $name;

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return TemplateOptions
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return TemplateOptions
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


}