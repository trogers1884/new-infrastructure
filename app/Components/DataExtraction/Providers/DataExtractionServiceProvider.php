<?php

namespace App\Components\DataExtraction\Providers;

use App\Components\DataExtraction\Contracts\ExtractorInterface;
use App\Components\DataExtraction\Services\Extractors\CsvExtractor;
use Illuminate\Support\ServiceProvider;

class DataExtractionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register our CSV extractor as the default implementation
        $this->app->bind(ExtractorInterface::class, CsvExtractor::class);

        // Register our CSV extractor specifically
        $this->app->bind('extractor.csv', function ($app) {
            return new CsvExtractor();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
