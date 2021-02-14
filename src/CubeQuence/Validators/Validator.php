<?php

namespace CQ\Validators;

use Respect\Validation\Validator as ValidatorBase;

class Validator
{
    /**
     * Execute validation
     *
     * @param ValidatorBase $validator
     * @param object $data
     *
     * @return void
     */
    protected static function validate(ValidatorBase $validator, object $data) : void
    {
        $validator->assert($data);
    }
}
