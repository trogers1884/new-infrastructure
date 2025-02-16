<?php

namespace Tests\Feature;

use App\Components\DataExtraction\Exceptions\ConnectionException;
use App\Components\DataExtraction\Exceptions\ExtractionException;
use App\Components\DataExtraction\Services\Extractors\CsvDataSource;
use App\Components\DataExtraction\Services\Extractors\CsvExtractor;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CsvExtractionTest extends TestCase
{
    private string $testCsvPath;
    private string $invalidCsvPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test CSV file in the storage/app/testing directory
        Storage::fake('local');

        // valid CSV
        $csvContent = "name,age,city\n" .
            "John Doe,30,Chicago\n" .
            "Jane Smith,25,Miami\n" .
            "Bob Johnson,45,New York";

        $this->testCsvPath = Storage::path('test_data.csv');
        file_put_contents($this->testCsvPath, $csvContent);

        // Invalid CSV (mismatched columns)
        $invalidCsvContent = "name,age,city\n" .
            "John Doe,30\n" .
            "Jane Smith,25,Miami,extra\n" .
            "Bob Johnson,invalid_age,New York";

        $this->invalidCsvPath = Storage::path('invalid_test_data.csv');
        file_put_contents($this->invalidCsvPath, $invalidCsvContent);
    }

    public function test_can_extract_data_from_csv()
    {
        // Create our data source
        $dataSource = new CsvDataSource(
            filePath: $this->testCsvPath,
            identifier: 'test_csv'
        );

        // Create our extractor
        $extractor = new CsvExtractor();

        // Test connection
        $this->assertTrue($dataSource->connect());
        $this->assertTrue($dataSource->isConnected());

        // Test extraction
        $data = $extractor->extract($dataSource);

        // Verify the data
        $this->assertIsArray($data);
        $this->assertCount(3, $data);
        $this->assertEquals([
            'name' => 'John Doe',
            'age' => '30',
            'city' => 'Chicago'
        ], $data[0]);

        // Test disconnection
        $dataSource->disconnect();
        $this->assertFalse($dataSource->isConnected());
    }

    public function test_extractor_supports_correct_source()
    {
        $dataSource = new CsvDataSource(
            filePath: $this->testCsvPath,
            identifier: 'test_csv'
        );

        $extractor = new CsvExtractor();

        $this->assertTrue($extractor->supports($dataSource));
    }

    public function test_last_extraction_is_maintained()
    {
        $dataSource = new CsvDataSource(
            filePath: $this->testCsvPath,
            identifier: 'test_csv'
        );

        $extractor = new CsvExtractor();

        $this->assertNull($extractor->getLastExtraction());

        $data = $extractor->extract($dataSource);

        $this->assertEquals($data, $extractor->getLastExtraction());
    }

//    protected function tearDown(): void
//    {
//        // Clean up our test file
//        if (file_exists($this->testCsvPath)) {
//            unlink($this->testCsvPath);
//        }
//
//        parent::tearDown();
//    }

    public function test_throws_exceptions_for_nonexistent_file()
    {
        $this->expectException(ConnectionException::class);
        $dataSource = new CsvDataSource(
            filePath: 'nonexistent.csv',
            identifier: 'test.csv'
        );

        $dataSource->connect();
    }

    public function test_throws_exception_for_invalid_file_format()
    {
        // Create a text file with .txt extension
        $textPath = Storage::path('test.txt');
        file_put_contents($textPath, 'This is not a CSV');
        $this->expectException(ConnectionException::class);
        $dataSource = new CsvDataSource(
            filePath: $textPath,
            identifier: 'test_txt'
        );
        $dataSource->connect();
    }

    public function test_handles_malformed_csv_data()
    {
        $dataSource = new CsvDataSource(
            filePath: $this->invalidCsvPath,
            identifier: 'invalid_csv'
        );
        $extractor = new CsvExtractor();
        $dataSource->connect();
        try {
            $data = $extractor->extract($dataSource);
            // Verify we can still process the file even with issues
            $this->assertIsArray($data);
            // Check that we have all rows (even problematic ones)
            $this->assertCount(3, $data);
            // First row should have null for missing city
            $this->assertArrayHasKey('city', $data[0]);
            $this->assertNull($data[0]['city']);
            // Verify we captured the age validation issue
            $this->assertEquals('invalid_age', $data[2]['age']);
        }   catch (ExtractionException $e){
            $this->fail("Should handle malformed CSV without throwing exception");
        }
    }

    protected function tearDown(): void
    {
        // Clean up our test files
        if (file_exists($this->testCsvPath)) {
            unlink($this->testCsvPath);
        }
        if (file_exists($this->invalidCsvPath)) {
            unlink($this->invalidCsvPath);
        }
        parent::tearDown();
    }

}
