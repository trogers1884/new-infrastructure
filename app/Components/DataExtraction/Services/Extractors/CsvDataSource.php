<?php

namespace App\Components\DataExtraction\Services\Extractors;

use App\Components\DataExtraction\Contracts\DataSourceInterface;
use App\Components\DataExtraction\Exceptions\ConnectionException;
use SplFileInfo;

class CsvDataSource implements DataSourceInterface
{
    private ?SplFileInfo $file = null;
    private bool $isConnected = false;

    /**
     * @param string $filePath Path to the CSV file
     */
    public function __construct(
        private readonly string $filePath,
        private readonly string $identifier
    ) {}

    /**
     * Verify file exists and is readable
     * @throws ConnectionException
     */
    public function connect(): bool
    {
        try {
            $this->file = new SplFileInfo($this->filePath);

            if (!$this->file->isReadable() || $this->file->getExtension() !== 'csv') {
                throw new ConnectionException("File is not readable or is not a CSV file");
            }

            $this->isConnected = true;
            return true;

        } catch (\Exception $e) {
            $this->isConnected = false;
            throw new ConnectionException("Failed to connect to CSV source: {$e->getMessage()}");
        }
    }

    public function disconnect(): void
    {
        $this->file = null;
        $this->isConnected = false;
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function getSourceType(): string
    {
        return 'csv';
    }

    public function getSourceIdentifier(): string
    {
        return $this->identifier;
    }

    public function getFile(): ?SplFileInfo
    {
        return $this->file;
    }
}
