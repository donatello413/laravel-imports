<?php

declare(strict_types=1);

namespace D413\LaravelImport\Example\Jobs;

use D413\LaravelImport\Example\Transfers\CategoryResponseDto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CategoryImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param Collection<CategoryResponseDto> $collection
     */
    public function __construct(
        public Collection $collection,
    ) {
    }

    /**
     * @return void
     */
    public function handle(): void
    {
//        $writer = app(CategoryWriterInterface::class);
//        $writer->storeCategories($this->collection);
    }
}
