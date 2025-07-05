<?php

namespace NaggadimDev\LaravelIspringLearn\DTO;

readonly class Department
{
    public function __construct(
        public string $departmentId,
        public string $name,
        public Subordination $subordination,
        public Subordination $coSubordination,
        public ?string $code = null,
        public ?string $parentDepartmentId = null,
    ) {}

    public static function fromJSON(array $json): self
    {
        return new self(
            $json['departmentId'],
            $json['name'],
            Subordination::fromJSON($json['subordination']),
            Subordination::fromJSON($json['coSubordination']),
            $json['code'] ?? null,
            $json['parentDepartmentId'] ?? null,
        );
    }
}