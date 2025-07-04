<?php

namespace NaggadimDev\LaravelIspringLearn\Responses;

readonly class AuthorizationResponse
{
    public function __construct(
        public string $accessToken,
        public int $expiresIn,
        public string $tokenType,
    ) {}

    public static function fromJSON(array $json): AuthorizationResponse
    {
        return new AuthorizationResponse(
            $json['access_token'],
            $json['expires_in'],
            $json['token_type']
        );
    }
}