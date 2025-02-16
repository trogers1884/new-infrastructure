<?php

namespace App\Components\DataExtraction\Contracts;

interface DataSourceInterface
{
    /**
     * Establish connection to the data source
     */
    public function connect(): bool;

    /**
     * Disconnect from the data source
     */
    public function disconnect(): void;

    /**
     * Check if currently connected
     */
    public function isConnected(): bool;

    /**
     * Get the type of source (e.g., 'csv', 'api')
     */
    public function getSourceType(): string;

    /**
     * Get unique identifier for this source
     */
    public function getSourceIdentifier(): string;
}
