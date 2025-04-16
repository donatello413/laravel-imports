<?php

declare(strict_types=1);

namespace D413\LaravelImport\Providers;

use D413\LaravelImport\Commands\ImportCategoriesCommand;
use D413\LaravelImport\Connector\EntityImportConnector;
use D413\LaravelImport\Connector\EntityImportConnectorInterface;
use D413\LaravelImport\Connector\WebsiteReceiver;
use D413\LaravelImport\Connector\WebsiteReceiverInterface;
use Illuminate\Support\ServiceProvider;

class ImportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerCommands();
        $this->registerPublishing();
        $this->registerDependency();
    }

    public function boot(): void
    {
    }

    /**
     * Register the package's commands.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportCategoriesCommand::class,
            ]);
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/import.php' => config_path('import.php'),
            ]);
        }
    }

    protected function registerDependency(): void
    {
        $this->app->singleton(WebsiteReceiverInterface::class, WebsiteReceiver::class);

        $this->app->singleton(EntityImportConnectorInterface::class, function ($app) {
            $client = $app->make(WebsiteReceiverInterface::class);

            return new EntityImportConnector($client);
        });
    }
}
