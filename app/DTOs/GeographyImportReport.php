<?php

namespace App\DTOs;

class GeographyImportReport
{
    /**
     * @param  array<string, array<string, int>>  $levels
     * @param  list<string>  $invalid
     * @param  list<string>  $manualReview
     * @param  list<string>  $missingRelationships
     */
    public function __construct(
        public string $datasetKey,
        public bool $dryRun,
        public bool $validateOnly,
        public array $levels,
        public array $invalid,
        public array $manualReview,
        public array $missingRelationships,
        public string $validationStatus,
        public ?string $checksum = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'dataset_key' => $this->datasetKey,
            'dry_run' => $this->dryRun,
            'validate_only' => $this->validateOnly,
            'validation_status' => $this->validationStatus,
            'checksum' => $this->checksum,
            'levels' => $this->levels,
            'invalid' => $this->invalid,
            'manual_review' => $this->manualReview,
            'missing_relationships' => $this->missingRelationships,
        ];
    }
}
