<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/23/2016
 * Time: 5:08 PM
 */

namespace N3vrax\DkUser\Twig;

use Zend\Form\Element\Button;
use Zend\Form\Element\Captcha;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Submit;

class FormElementExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'dk-user';
    }

    public function getTests()
    {
        return [
            new \Twig_SimpleTest('submitElement', [$this, 'isSubmit']),
            new \Twig_SimpleTest('buttonElement', [$this, 'isButton']),
            new \Twig_SimpleTest('checkboxElement', [$this, 'isCheckbox']),
            new \Twig_SimpleTest('captchaElement', [$this, 'isCaptcha']),
        ];
    }

    public function isSubmit($value)
    {
        return ($value instanceof Submit);
    }

    public function isButton($value)
    {
        return ($value instanceof Button);
    }

    public function isCheckbox($value)
    {
        return ($value instanceof Checkbox);
    }

    public function isCaptcha($value)
    {
        return ($value instanceof Captcha);
    }
}
