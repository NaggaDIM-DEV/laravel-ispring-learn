<?php

namespace NaggadimDev\LaravelIspringLearn\Responses;

use NaggadimDev\LaravelIspringLearn\DTO\Department;

readonly class DepartmentsPaginatedResponse
{
    public function __construct(
        public array $departments,
        public ?string $nextPageToken = null,
    ) {}

    public static function fromJSON(array $json): self
    {
        return new self(
            departments: array_map(fn($department) => Department::fromJSON($department), $json['departments']),
            nextPageToken: $json['nextPageToken'] ?? null,
        );
    }
}