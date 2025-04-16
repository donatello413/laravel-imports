<?php

namespace D413\LaravelImport\Exceptions;

class ImportEmptyItemException extends ImportException
{
    public function __construct(string $message = 'The collection is empty, the last item was not found.')
    {
        parent::__construct($message);
    }
}