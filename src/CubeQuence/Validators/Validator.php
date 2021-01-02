<?php

namespace CQ\Validators;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as ValidatorBase;

class Validator
{
    /**
     * Execute validation.
     *
     * @param ValidatorBase $validator
     * @param object        $data
     *
     * @throws \Throwable
     */
    protected static function validate(ValidatorBase $validator, $data)
    {
        try {
            $validator->assert($data);
        } catch (NestedValidationException $e) {
            throw new \Throwable(
                json_encode($e->getMessages())
            );
        }
    }
}
