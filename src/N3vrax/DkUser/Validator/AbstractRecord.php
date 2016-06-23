<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/23/2016
 * Time: 9:01 PM
 */

namespace N3vrax\DkUser\Validator;

use N3vrax\DkUser\Exception\InvalidArgumentException;
use N3vrax\DkUser\Exception\RuntimeException;
use N3vrax\DkUser\Mapper\UserMapperInterface;
use Zend\Validator\AbstractValidator;

abstract class AbstractRecord extends AbstractValidator
{
    /**
     * Error constants
     */
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';
    /**
     * @var array Message templates
     */
    protected $messageTemplates = array(
        self::ERROR_NO_RECORD_FOUND => "No record matching the input was found",
        self::ERROR_RECORD_FOUND    => "A record matching the input was found",
    );
    /**
     * @var UserMapperInterface
     */
    protected $mapper;
    
    /**
     * @var string
     */
    protected $key;
    
    /**
     * Required options are:
     *  - key     Field to use, 'email' or 'username'
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (!array_key_exists('key', $options)) {
            throw new InvalidArgumentException('No key provided');
        }
        $this->setKey($options['key']);
        parent::__construct($options);
    }

    /**
     * getMapper
     *
     * @return UserMapperInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * setMapper
     *
     * @param UserMapperInterface $mapper
     * @return AbstractRecord
     */
    public function setMapper(UserMapperInterface $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * Get key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set key.
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Grab the user from the mapper
     *
     * @param string $value
     * @return mixed
     */
    protected function query($value)
    {
        $result = false;
        switch ($this->getKey()) {
            case 'email':
                $result = $this->getMapper()->findUserBy('email', $value);
                break;

            case 'username':
                $result = $this->getMapper()->findUserBy('username', $value);
                break;

            default:
                throw new RuntimeException('Invalid key used in DkUser validator');
                break;
        }
        return $result;
    }
}