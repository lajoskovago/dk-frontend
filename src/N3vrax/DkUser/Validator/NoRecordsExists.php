<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/23/2016
 * Time: 9:09 PM
 */

namespace N3vrax\DkUser\Validator;

class NoRecordsExists extends AbstractRecord
{
    public function isValid($value)
    {
        $valid = true;
        $this->setValue($value);
        $result = $this->query($value);
        if ($result) {
            $valid = false;
            $this->error(self::ERROR_RECORD_FOUND);
        }
        return $valid;
    }
}