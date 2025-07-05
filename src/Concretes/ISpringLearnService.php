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
use NaggadimDev\LaravelIspringLearn\DTO\Department;
use NaggadimDev\LaravelIspringLearn\DTO\Subordination;
use NaggadimDev\LaravelIspringLearn\DTO\UserProfile;
use NaggadimDev\LaravelIspringLearn\Responses\AuthorizationResponse;
use NaggadimDev\LaravelIspringLearn\Responses\DepartmentsPaginatedResponse;
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
                'url'       => $url,
                'query'     => $query
            ]);
        }

        $response = Http::withHeaders($this->defaultHeaders())->get($url, $query);
        if(config('ispring-learn.logging.enabled')) {
            $this->logger()->debug("[RESPONSE][GET][$requestID][{$response->status()}]: $url", [
                'status'    => $response->status(),
                'response'  => $response->body()
            ]);
        }

        return $response;
    }

    /**
     * @throws ConnectionException
     */
    public function postRequest(string $url, ?array $data = null): PromiseInterface|Response
    {
        $url = $this->buildUrl($url);
        $requestID = (string) Str::uuid();
        if(config('ispring-learn.logging.enabled')) {
            $this->logger()->info("[REQUEST][POST][$requestID]: $url", [
                'data'      => $data
            ]);
        }

        $response = Http::withHeaders($this->defaultHeaders())->post($url, $data);
        if(config('ispring-learn.logging.enabled')) {
            $this->logger()->debug("[RESPONSE][POST][$requestID][{$response->status()}]: $url", [
                'status'    => $response->status(),
                'response'  => $response->body()
            ]);
        }

        return $response;
    }

    /**
     * @throws ConnectionException
     */
    public function deleteRequest(string $url): PromiseInterface|Response
    {
        $url = $this->buildUrl($url);
        $requestID = (string) Str::uuid();
        if(config('ispring-learn.logging.enabled')) {
            $this->logger()->info("[REQUEST][DELETE][$requestID]: $url");
        }

        $response = Http::withHeaders($this->defaultHeaders())->delete($url);
        if(config('ispring-learn.logging.enabled')) {
            $this->logger()->debug("[RESPONSE][DELETE][$requestID][{$response->status()}]: $url", [
                'status'    => $response->status(),
            ]);
        }

        return $response;
    }

    public function getDepartmentsPaginated(?array $parameters = null): DepartmentsPaginatedResponse
    {
        return DepartmentsPaginatedResponse::fromJSON(
            $this->getRequest('index.php/api/v2/departments', $parameters)->json()
        );
    }

    public function getDepartments(): array
    {
        return array_map(
            fn($department) => Department::fromJSON($department),
            $this->getRequest('index.php/api/v2/department')->json()
        );
    }

    public function getDepartment(string $departmentId): Department
    {
        return Department::fromJSON(
            $this->getRequest("index.php/api/v2/department/$departmentId")->json()
        );
    }

    public function addDepartment(string $parentDepartmentId, string $name, ?string $code = null, ?Subordination $subordination = null, ?Subordination $coSubordination = null): string
    {
        return $this->postRequest('index.php/api/v2/department', [
            'parentDepartmentId' => $parentDepartmentId,
            'name' => $name,
            'code' => $code,
            'subordination' => $subordination?->toJSON(),
            'coSubordination' => $coSubordination?->toJSON(),
        ])->json();
    }

    public function editDepartment(string $departmentId, ?string $parentDepartmentId = null, ?string $name = null, ?string $code = null, ?Subordination $subordination = null, ?Subordination $coSubordination = null): bool
    {
        $data = [];
        if(!empty($parentDepartmentId)) { $data['parentDepartmentId'] = $parentDepartmentId; }
        if(!empty($name)) { $data['name'] = $name; }
        if(!empty($code)) { $data['code'] = $code; }
        if(!empty($subordination)) { $data['subordination'] = $subordination->toJSON(); }
        if(!empty($coSubordination)) { $data['coSubordination'] = $coSubordination->toJSON(); }

        return $this->postRequest("index.php/api/v2/department/$departmentId", $data)->status() === 204;
    }

    public function deleteDepartment(string $departmentId): bool
    {
        return $this->deleteRequest("index.php/api/v2/department/$departmentId")->status() === 204;
    }

    public function getUsersPaginated(?array $parameters = null): UsersPaginatedResponse
    {
        return UsersPaginatedResponse::fromJSON(
            $this->getRequest('index.php/api/v2/users', $parameters)->json()
        );
    }

    public function getUser(string $userId): UserProfile
    {
        return UserProfile::fromJSON(
            $this->getRequest("index.php/api/v2/user/$userId/v2")->json()
        );
    }
}