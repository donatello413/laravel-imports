<?php

declare(strict_types=1);

namespace D413\LaravelImport\Example\Events;

use D413\LaravelImport\Example\Transfers\CategoryResponseDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;

class CategoryImportEvent
{
    use Dispatchable;

    /**
     * @param Collection<CategoryResponseDto> $categories
     */
    public function __construct(
        public Collection $categories
    ) {
    }
}
