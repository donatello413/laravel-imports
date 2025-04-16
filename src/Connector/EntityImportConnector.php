<?php

namespace D413\LaravelImport\Connector;

use D413\LaravelImport\Exceptions\ImportEmptyItemException;
use D413\LaravelImport\Exceptions\ImportInvalidIdException;
use D413\LaravelImport\Exceptions\ImportTransportException;
use D413\LaravelImport\Transfers\ImportRequestDto;
use Generator;
use Illuminate\Support\Collection;
use Throwable;

final class EntityImportConnector implements EntityImportConnectorInterface
{
    public function __construct(
        protected WebsiteReceiverInterface $client,
    ) {
    }

    /**
     * @template T of object
     * @return Generator<Collection<int, T>>
     * @throws ImportTransportException
     */
    public function stream(ImportRequestDto $importRequestDto): Generator
    {
        do {
            $collection = $this->fetchCollection($importRequestDto);

            if ($collection->isEmpty()) {
                break;
            }

            yield $collection;

            $importRequestDto->page++;
        } while (true);
    }

    /**
     * @template T of object
     * @param Collection<int, T> $collection
     * @return positive-int
     * @throws ImportInvalidIdException
     * @throws ImportEmptyItemException
     */
    private function getLastIdFromCollection(Collection $collection): int
    {
        $lastItem = $collection->last();

        if (!is_object($lastItem) || !property_exists($lastItem, 'id')) {
            throw new ImportEmptyItemException();
        }

        $id = $lastItem->id;

        if (!is_int($id) || $id <= 0) {
            throw new ImportInvalidIdException();
        }

        return $id;
    }

    /**
     * @template T of object
     * @return Collection<int, T>
     * @throws ImportTransportException
     */
    private function fetchCollection(ImportRequestDto $importRequestDto): Collection
    {
        try {
            $result = $this->client->receive($importRequestDto);

            return collect($result->all());
        } catch (Throwable $e) {
            throw new ImportTransportException(
                message: 'Error while accessing the import client.', code: 0, previous: $e
            );
        }
    }
}