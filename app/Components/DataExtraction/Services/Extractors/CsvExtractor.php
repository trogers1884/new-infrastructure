<?php

namespace App\Components\DataExtraction\Services\Extractors;

use App\Components\DataExtraction\Contracts\DataSourceInterface;
use App\Components\DataExtraction\Contracts\ExtractorInterface;
use App\Components\DataExtraction\Exceptions\ExtractionException;

class CsvExtractor implements ExtractorInterface
{
    private ?array $lastExtraction = null;
    private array $extractionErrors = [];
    private ?array $headers = null;

    /**
     * Extract data from a CSV source
     *
     * @throws ExtractionException
     */
    public function extract(DataSourceInterface $source): array
    {
        $this->resetState();

        if (!$this->supports($source)) {
            throw new ExtractionException("Unsupported data source type");
        }

        if (!$source->isConnected()) {
            $source->connect();
        }

        /** @var CsvDataSource $source */
        $file = $source->getFile();

        try {
            $handle = fopen($file->getRealPath(), 'r');
            if ($handle === false) {
                throw new ExtractionException("Could not open file for reading");
            }

            // Read and validate headers
            $this->headers = $this->readHeaders($handle);
            if (empty($this->headers)) {
                throw new ExtractionException("No headers found in CSV file");
            }

            $data = $this->processRows($handle);
            fclose($handle);
            $this->lastExtraction = $data;
            return $data;
        } catch (\Exception $e) {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            throw new ExtractionException("Failed to extract CSV data: {$e->getMessage()}");
        }
    }

    private function readHeaders($handle): array
    {
        $headers = fgetcsv($handle);
        if (!$headers) {
            return [];
        }
        // Clean up headers (trim whitespace, remove empty columns)
        return array_map(
            fn($header) => trim($header),
            array_filter($headers, fn($header) => !empty(trim($header)))
        );
    }

//            if ($headers === false) {
//                throw new ExtractionException("Could not read CSV headers");
//            }
//
//            $data = [];
//            while (($row = fgetcsv($handle)) !== false) {
//                // Combine headers with row data
//                $data[] = array_combine($headers, $row);
//            }
//
//            fclose($handle);
//
//            $this->lastExtraction = $data;
//            return $data;
//
//        } catch (\Exception $e) {
//            throw new ExtractionException("Failed to extract CSV data: {$e->getMessage()}");
//        }
//    }


    private function processRows($handle): array
    {
        $data = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            // Handle row having different number of columns than headers
            if(count($row) !== count($this->headers)) {
                $this->addError(
                    $rowNumber,
                    "Row has " . count($row) . "columns, expected " . count($this->headers)
                );
                // Pad or truncate row to match header count
                if (count($row) < count($this->headers)) {
                    $row = array_pad($row, count($this->headers), null);
                }   else {
                    $row = array_slice($row, 0, count($this->headers));
                }
            }
            // Clean row data
            $row = array_map(fn($value) => $this->cleanValue($value), $row);
            // Combine with headers
            $rowData = array_combine($this->headers, $row);
            $data[] = $rowData;
        }
        return $data;
    }

    private function cleanValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value  = trim($value);
        return $value === '' ? null : $value;
    }

    private function addError(int $row, string $message): void
    {
        $this->extractionErrors[] = [
            'row' => $row,
            'message' => $message,
            'timestamp' => now()
        ];
    }

    private function resetState(): void
    {
        $this->extractionErrors = [];
        $this->headers = null;
    }

    public function getExtractionErrors(): array
    {
        return $this->extractionErrors;
    }

    public function supports(DataSourceInterface $source): bool
    {
        return $source instanceof CsvDataSource && $source->getSourceType() === 'csv';
    }

    public function getLastExtraction(): ?array
    {
        return $this->lastExtraction;
    }

    public function getExtractorName(): string
    {
        return 'csv_extractor';
    }
}
