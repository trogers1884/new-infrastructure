<?php

namespace App\Components\DataExtraction\Contracts;

interface ExtractorInterface
{
    /**
     * Extract data from the given source
     */
    public function extract(DataSourceInterface $source): array;

    /**
     * Check if this extractor supports the given source
     */
    public function supports(DataSourceInterface $source): bool;

    /**
     * Get the data from the last extraction
     */
    public function getLastExtraction(): ?array;

    /**
     * Get the name of this extractor
     */
    public function getExtractorName(): string;
}
