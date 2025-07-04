<?php

namespace NaggadimDev\LaravelIspringLearn\DTO;

readonly class UserProfileField
{
    public function __construct(
        public string $name,
        public string $value,
    ) {}

    public static function fromJSON(array $json): UserProfileField
    {
        return new self(
            name: $json['name'],
            value: $json['value'],
        );
    }
}