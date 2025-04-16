<?php

declare(strict_types=1);

namespace D413\LaravelImport\Transfers;

use Spatie\LaravelData\Data;

/**
 * @template T of object
 */
final class ImportRequestDto extends Data
{
    public function __construct(
        public string $endpoint,
        public int $limit = 1,
        public int $page = 1,
        /** @var class-string<T> $resultDto */
        public string $resultDto,
    ) {
    }
}
