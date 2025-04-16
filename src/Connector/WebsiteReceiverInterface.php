<?php

declare(strict_types=1);

namespace D413\LaravelImport\Connector;

use D413\LaravelImport\Transfers\ImportRequestDto;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

/**
 * @template T of Data
 */
interface WebsiteReceiverInterface
{
    /**
     * @return Collection<int|string, T>
     */
    public function receive(ImportRequestDto $requestDto): Collection;
}
