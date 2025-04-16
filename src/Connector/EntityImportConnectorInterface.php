<?php

namespace D413\LaravelImport\Connector;

use D413\LaravelImport\Exceptions\ImportTransportException;
use D413\LaravelImport\Transfers\ImportRequestDto;
use Generator;
use Illuminate\Support\Collection;

interface EntityImportConnectorInterface
{
    /**
     * @template T of object
     * @return Generator<Collection<int, T>>
     * @throws ImportTransportException
     */
    public function stream(ImportRequestDto $importRequestDto): Generator;
}