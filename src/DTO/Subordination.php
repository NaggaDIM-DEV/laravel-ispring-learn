<?php

namespace NaggadimDev\LaravelIspringLearn\DTO;

readonly class Subordination
{
    public function __construct(
        public string $subordinationType,
        public ?string $supervisorId,
    ) {}

    public static function fromJSON(array $json): self
    {
        return new self(
            $json['subordinationType'],
            $json['supervisorId'] ?? null,
        );
    }

    public function toJSON(): array
    {
        return [
            'subordinationType' => $this->subordinationType,
            'supervisorId'      => $this->supervisorId,
        ];
    }
}