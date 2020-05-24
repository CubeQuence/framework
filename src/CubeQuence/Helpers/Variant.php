<?php

namespace CQ\Helpers;

use Exception;
use CQ\Config\Config;

class Variant
{
    private string $user;
    private string $type;
    private $current_value;

    /**
     * Define variant variables
     * 
     * @param array $data
     * 
     * @return void
     */
    public function __construct($data)
    {
        $this->user = $data['user'];
        $this->type = $data['type'];
        $this->current_value = $data['current_value'] ?: null;
        $this->configured_value = Config::get("variants.{$this->user}.{$this->type}", null);
    }

    /**
     * Check if user license limit is reached
     *
     * @return bool
     */
    public function limitReached()
    {
        if (!is_int($this->configured_value)) {
            throw new Exception('Invalid operation on non-number');
        }

        return $this->current_value < $this->configured_value;
    }

    /**
     * Return configured value
     *
     * @return mixed
     */
    public function configuredValue()
    {
        return $this->configured_value;
    }
}
