<?php

namespace D413\LaravelImport\Exceptions;

class ImportInvalidIdException extends ImportException
{
    public function __construct(string $message = 'ID must be a positive integer.')
    {
        parent::__construct($message);
    }
}