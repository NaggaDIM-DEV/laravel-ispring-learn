<?php

namespace NaggadimDev\LaravelIspringLearn\Responses;

use NaggadimDev\LaravelIspringLearn\DTO\UserProfile;

readonly class UsersPaginatedResponse
{
    public function __construct(
        public array $userProfiles,
        public ?string $nextPageToken = null,
    ) {}

    public static function fromJSON(array $json): UsersPaginatedResponse
    {
        return new self(
            userProfiles: array_map(fn ($profile) => UserProfile::fromJSON($profile), $json['userProfiles']),
            nextPageToken: $json['nextPageToken'] ?? null,
        );
    }
}