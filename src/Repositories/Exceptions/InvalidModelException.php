<?php

namespace Lnch\LaravelToolkit\Repositories\Exceptions;

use Throwable;

class InvalidModelException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if ($message === '') {
            $message = 'The model passed to the repository was invalid';
        }

        parent::__construct($message, $code, $previous);
    }
}
