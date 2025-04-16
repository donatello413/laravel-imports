<?php

namespace D413\LaravelImport\Exceptions;

use Exception;
use Throwable;

abstract class ImportException extends Exception
{
    protected ?Throwable $previousException;

    public function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->previousException = $previous;
    }
}