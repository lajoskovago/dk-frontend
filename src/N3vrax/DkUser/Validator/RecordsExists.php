<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/23/2016
 * Time: 9:10 PM
 */

namespace N3vrax\DkUser\Validator;

class RecordExists extends AbstractRecord
{
    public function isValid($value)
    {
        $valid = true;
        $this->setValue($value);
        $result = $this->query($value);
        if (!$result) {
            $valid = false;
            $this->error(self::ERROR_NO_RECORD_FOUND);
        }
        return $valid;
    }
}