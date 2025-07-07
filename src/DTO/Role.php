<?php

namespace NaggadimDev\LaravelIspringLearn\DTO;

readonly class Role
{
    public function __construct(
        public string $roleId,
        /** @var null|array<string> $manageableDepartmentIds */
        public ?array $manageableDepartmentIds = null,
    ) {}

    public function toJSON(): array
    {
        return [
            'roleId' => $this->roleId,
            'manageableDepartmentIds' => $this->manageableDepartmentIds,
        ];
    }
}