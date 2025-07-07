<?php

namespace NaggadimDev\LaravelIspringLearn\Concretes;

use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NaggadimDev\LaravelIspringLearn\Contracts\ISpringLearnServiceContract;
use NaggadimDev\LaravelIspringLearn\DTO\Department;
use NaggadimDev\LaravelIspringLearn\DTO\Role;
use NaggadimDev\LaravelIspringLearn\DTO\Subordination;
use NaggadimDev\LaravelIspringLearn\DTO\UserProfile;
use NaggadimDev\LaravelIspringLearn\DTO\UserProfileField;
use NaggadimDev\LaravelIspringLearn\Exceptions\BadRequestException;
use NaggadimDev\LaravelIspringLearn\Exceptions\ISpringLearnHTTPException;
use NaggadimDev\LaravelIspringLearn\Exceptions\PermissionDeniedException;
use NaggadimDev\LaravelIspringLearn\Exceptions\UnauthorizedException;
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

        if(config('ispring-learn.auth_type', 'login') === 'api-key') {
            $defaultHeaders['Authorization'] = 'Bearer ' . $this->getAuthorizationToken();
        }

        if(config('ispring-learn.auth_type', 'login') === 'login') {
            $defaultHeaders = array_merge($defaultHeaders, [
                'X-Auth-Account-Url'    => config('ispring-learn.account_url'),
                'X-Auth-Email'          => config('ispring-learn.username'),
                'X-Auth-Password'       => config('ispring-learn.password'),
            ]);
        }

        return $defaultHeaders;
    }

    /**
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    protected function validateResponse(PromiseInterface|Response &$response): void
    {
        if($response->failed()) {
            throw match ($response->status()) {
                400 => new BadRequestException($response),
                401 => new UnauthorizedException($response),
                403 => new PermissionDeniedException($response),
                default => new ISpringLearnHTTPException($response),
            };
        }
    }

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

        $this->validateResponse($response);

        return $response;
    }

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

        $this->validateResponse($response);

        return $response;
    }

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

        $this->validateResponse($response);

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

    public function getUsers(?array $parameters = null): array
    {
        return array_map(
            fn($e) => UserProfile::fromJSON($e),
            $this->getRequest('index.php/api/v2/user/v2')->json()
        );
    }

    public function getUser(string $userId): UserProfile
    {
        return UserProfile::fromJSON(
            $this->getRequest("index.php/api/v2/user/$userId/v2")->json()
        );
    }

    public function addUser(
        string $departmentId,
        string $login,
        array $fields,
        ?string $email = null,
        ?string $password = null,
        bool $sendLoginEmail = false,
        ?string $invitationMessage = null,
        bool $sendLoginSMS = false,
        ?string $invitationSMSMessage = null,
        ?string $role = null,
        ?string $roleId = null,
        ?array $manageableDepartmentIds = null,
        ?array $groups = null,
        ?array $roles = null
    ): string
    {
        return $this->postRequest('index.php/api/v2/user', [
            'departmentId' => $departmentId,
            'login' => $login,
            'fields' => array_map(fn(UserProfileField $field) => $field->toJSON(), $fields),
            'email' => $email,
            'password' => $password,
            'sendLoginEmail' => $sendLoginEmail,
            'invitationMessage' => $invitationMessage,
            'sendLoginSMS' => $sendLoginSMS,
            'invitationSMSMessage' => $invitationSMSMessage,
            'role' => $role,
            'roleId' => $roleId,
            'manageableDepartmentIds' => $manageableDepartmentIds,
            'groups' => $groups,
            'roles' => $roles ? array_map(fn(Role $role) => $role->toJSON(), $roles) : null,
        ])->json();
    }

    public function editUser(
        string $userId,
        ?string $departmentId = null,
        ?array $fields = null,
        ?string $role = null,
        ?string $roleId = null,
        ?array $manageableDepartmentIds = null,
        ?array $groupIds = null,
        ?array $roles = null
    ): bool
    {
        $data = [];
        if(!empty($departmentId))   { $data['departmentId'] = $departmentId; }
        if(!empty($fields))         { $data['fields'] = array_map(fn(UserProfileField $field) => $field->toJSON(), $fields); }
        if(!empty($role))           { $data['role'] = $role; }
        if(!empty($roleId))         { $data['roleId'] = $roleId; }
        if(!empty($manageableDepartmentIds)) { $data['manageableDepartmentIds'] = $manageableDepartmentIds; }
        if(!empty($groupIds))       { $data['groupIds'] = $groupIds; }
        if(!empty($roles))          { $data['roles'] = array_map(fn(Role $role) => $role->toJSON(), $roles); }

        return $this->postRequest("index.php/api/v2/user/$userId", $data)->status() === 204;
    }

    public function editUserPassword(string $userId, string $password): bool
    {
        return $this->postRequest("index.php/api/v2/user/$userId/password", [
            'password' => $password,
        ])->successful();
    }

    public function editUserStatus(string $userId, int $status): bool
    {
        return $this->postRequest("index.php/api/v2/user/$userId/status", [
            'status' => $status,
        ])->successful();
    }

    public function activateUser(string $userId): bool
    {
        return $this->editUserStatus($userId, 1);
    }

    public function deactivateUser(string $userId): bool
    {
        return $this->editUserStatus($userId, 3);
    }

    public function terminateUser(string $userId): bool
    {
        return $this->editUserStatus($userId, 5);
    }

    public function deactivateUserScheduled(string $userId, Carbon $date): bool
    {
        return $this->postRequest("index.php/api/v2/user/$userId/scheduled_deactivation", [
            'date' => $date->format('Y-m-d'),
        ])->successful();
    }

    public function deactivateUserScheduledCancel(string $userId): bool
    {
        return $this->deleteRequest("index.php/api/v2/user/$userId/scheduled_deactivation")
            ->successful();
    }

    public function terminateUserScheduled(string $userId, Carbon $date): bool
    {
        return $this->postRequest("index.php/api/v2/user/$userId/scheduled_termination", [
            'date' => $date->format('Y-m-d'),
        ])->successful();
    }

    public function terminateUserScheduledCancel(string $userId): bool
    {
        return $this->deleteRequest("index.php/api/v2/user/$userId/scheduled_termination")
            ->successful();
    }

    public function massActivateUsers(array $userIds): bool
    {
        return $this->postRequest("index.php/api/v2/users/activate", ['userIds' => $userIds])
            ->successful();
    }

    public function massDeactivateUsers(array $userIds): bool
    {
        return $this->postRequest("index.php/api/v2/users/deactivate", ['userIds' => $userIds])
            ->successful();
    }

    public function massTerminateUsers(array $userIds): bool
    {
        return $this->postRequest("index.php/api/v2/users/terminate", ['userIds' => $userIds])
            ->successful();
    }

    public function deleteUser(string $userId): bool
    {
        return $this->deleteRequest("index.php/api/v2/user/$userId")
            ->successful();
    }
}