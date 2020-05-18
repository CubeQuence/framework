<?php

namespace CQ\Validators;

use Exception;
use Respect\Validation\Validator as ValidatorBase;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    /**
     * Execute validation
     * 
     * @param ValidatorBase $validator
     * @param object $data
     *
     * @return void
     * @throws Exception
     */
    protected static function validate(ValidatorBase $validator, $data)
    {
        try {
            $validator->assert($data);
        } catch (NestedValidationException $e) {
            throw new Exception(
                json_encode($e->getMessages())
            );
        }
    }
}
