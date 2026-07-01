<?php

namespace App\Exceptions;

use Exception;

class InvalidStateTransitionException extends Exception
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Cannot transition registration from state '{$from}' to '{$to}'.");
    }
}
