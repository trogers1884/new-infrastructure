<?php

namespace Tests\Feature;

use App\Components\DataExtraction\Contracts\ExtractorInterface;
use App\Components\DataExtraction\Services\Extractors\CsvExtractor;
use Tests\TestCase;

class DataExtractionProviderTest extends TestCase
{
    public function test_csv_extractor_is_bound()
    {
        // Test the interface binding
        $extractor = app(ExtractorInterface::class);
        $this->assertInstanceOf(CsvExtractor::class, $extractor);

        // Test the named binding
        $csvExtractor = app('extractor.csv');
        $this->assertInstanceOf(CsvExtractor::class, $csvExtractor);
    }
}
