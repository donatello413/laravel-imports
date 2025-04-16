<?php

namespace D413\LaravelImport\Exceptions;

use Throwable;

class ImportTransportException extends ImportException
{
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}