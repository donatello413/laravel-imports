<?php

declare(strict_types=1);

namespace D413\LaravelImport\Example\Listeners;

use D413\LaravelImport\Example\Events\CategoryImportEvent;
use D413\LaravelImport\Example\Jobs\CategoryImportJob;

class CategoryImportListener
{
    public function handle(CategoryImportEvent $event): void
    {
        CategoryImportJob::dispatch($event->categories);
    }
}
