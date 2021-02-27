<?php

declare(strict_types=1);

namespace CQ\Validators;

use Respect\Validation\Validator as ValidatorBase;

class Validator
{
    /**
     * Execute validation
     */
    protected static function validate(ValidatorBase $validator, object $data): void
    {
        $validator->assert(input: $data);
    }
}
