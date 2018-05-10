<?php

namespace Mr\Sdk\Exception;


class InvalidCredentialsException extends MrException
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct('Invalid credentials', 401);
    }
}