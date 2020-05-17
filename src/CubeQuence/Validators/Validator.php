<?php

namespace CQ\Validators;

use Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    /**
     * Execute validation
     * 
     * @param v $validator
     * @param object $data
     *
     * @return void
     * @throws Exception
     */
    protected static function validate(v $validator, $data)
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
