<?php

namespace NaggadimDev\LaravelIspringLearn\Concretes;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NaggadimDev\LaravelIspringLearn\Contracts\ISpringLearnServiceContract;
use NaggadimDev\LaravelIspringLearn\Responses\AuthorizationResponse;
use NaggadimDev\LaravelIspringLearn\Responses\UsersPaginatedResponse;
use Psr\Log\LoggerInterface;

class ISpringLearnService implements ISpringLearnServiceContract
{
    protected function logger(): LoggerInterface
    {
        return Log::build(config('ispring-learn.logging.channel'));
    }

    protected function buildUrl(string $url): string
    {
        $accountURL = str_ends_with(config('ispring-learn.account_url'), '/')
            ? config('ispring-learn.account_url')
            : config('ispring-learn.account_url') . '/';
        $url = str_starts_with($url, '/')
            ? substr($url, 1)
            : $url;

        return $accountURL . $url;
    }

    protected function getAuthorizationToken(): string
    {
        if(Cache::has('ispring-learn-token')) { return Cache::get('ispring-learn-token'); }

        $response = AuthorizationResponse::fromJSON(Http::withHeader('Accept', 'application/json')
            ->asForm()
            ->post($this->buildUrl('/api/v3/token'), [
                'grant_type'    => 'client_credentials',
                'client_id'     => config('ispring-learn.client_id'),
                'client_secret' => config('ispring-learn.client_secret'),
            ])
            ->json());

        Cache::set('ispring-learn-token', $response->accessToken, $response->expiresIn);

        return $response->accessToken;
    }

    protected function defaultHeaders(): array
    {
        $defaultHeaders = ['Accept' => 'application/json'];

        if(config('ispring-learn.auth_type', 'api-key') === 'api-key') {
            $defaultHeaders['Authorization'] = 'Bearer ' . $this->getAuthorizationToken();
        }

        if(config('ispring-learn.auth_type', 'api-key') === 'login') {
            $defaultHeaders = array_merge($defaultHeaders, [
                'X-Auth-Account-Url'    => config('ispring-learn.account_url'),
                'X-Auth-Email'          => config('ispring-learn.username'),
                'X-Auth-Password'       => config('ispring-learn.password'),
            ]);
        }

        return $defaultHeaders;
    }

    /**
     * @throws ConnectionException
     */
    public function getRequest(string $url, ?array $query = null): PromiseInterface|Response
    {
        $url = $this->buildUrl($url);
        $requestID = (string) Str::uuid();
        if(config('ispring-learn.logging.enabled')) {
            $this->logger()->info("[REQUEST][GET][$requestID]: $url", [
                'requestID' => $requestID,
                'url'       => $url,
                'query'     => $query
            ]);
        }

        $response = Http::withHeaders($this->defaultHeaders())->get($url, $query);
        if(config('ispring-learn.logging.enabled')) {
            $this->logger()->debug("[RESPONSE][GET][$requestID][{$response->status()}]: $url", [
                'requestID' => $requestID,
                'url'       => $url,
                'query'     => $query,
                'status'    => $response->status(),
                'response'  => $response->body()
            ]);
        }

        return $response;
    }

    public function usersPaginated(?array $parameters = null): UsersPaginatedResponse
    {
        return UsersPaginatedResponse::fromJSON(
            $this->getRequest('index.php/api/v2/users', $parameters)->json()
        );
    }
}