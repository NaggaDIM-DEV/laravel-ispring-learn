<?php

namespace NaggadimDev\LaravelIspringLearn\DTO;

readonly class UserProfile
{
    public function __construct(
        public string $userId,
        public string $roleId,
        public string $role,
        public string $departmentId,
        public int $status,
        public array $fields,
    ) {}

    public static function fromJSON(array $json): UserProfile
    {
        return new self(
            userId: $json['userId'],
            roleId: $json['roleId'],
            role: $json['role'],
            departmentId: $json['departmentId'],
            status: $json['status'],
            fields: array_map(fn ($field) => UserProfileField::fromJSON($field), $json['fields']),
        );
    }
}