<?php

namespace NaggadimDev\LaravelIspringLearn\Contracts;

use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use NaggadimDev\LaravelIspringLearn\DTO\Department;
use NaggadimDev\LaravelIspringLearn\DTO\Role;
use NaggadimDev\LaravelIspringLearn\DTO\Subordination;
use NaggadimDev\LaravelIspringLearn\DTO\UserProfile;
use NaggadimDev\LaravelIspringLearn\DTO\UserProfileField;
use NaggadimDev\LaravelIspringLearn\Exceptions\BadRequestException;
use NaggadimDev\LaravelIspringLearn\Exceptions\ISpringLearnHTTPException;
use NaggadimDev\LaravelIspringLearn\Exceptions\PermissionDeniedException;
use NaggadimDev\LaravelIspringLearn\Exceptions\UnauthorizedException;
use NaggadimDev\LaravelIspringLearn\Responses\DepartmentsPaginatedResponse;
use NaggadimDev\LaravelIspringLearn\Responses\UsersPaginatedResponse;

interface ISpringLearnServiceContract
{
    /**
     * @param string $url
     * @param array|null $query
     * @return PromiseInterface|Response
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function getRequest(string $url, ?array $query = null): PromiseInterface|Response;

    /**
     * @param string $url
     * @param array|null $data
     * @return PromiseInterface|Response
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function postRequest(string $url, ?array $data = null): PromiseInterface|Response;

    /**
     * @param string $url
     * @return PromiseInterface|Response
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function deleteRequest(string $url): PromiseInterface|Response;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=24677928
     * @param null|array{
     *     pageSize: null|int,
     *     pageToken: null|string,
     * } $parameters
     * @return DepartmentsPaginatedResponse
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function getDepartmentsPaginated(?array $parameters = null): DepartmentsPaginatedResponse;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18808974
     * @return array<Department>
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function getDepartments(): array;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18809544
     * @param string $departmentId
     * @return Department
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function getDepartment(string $departmentId): Department;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18808970
     * @param string $parentDepartmentId
     * @param string $name
     * @param string|null $code
     * @param Subordination|null $subordination
     * @param Subordination|null $coSubordination
     * @return string
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function addDepartment(
        string $parentDepartmentId,
        string $name,
        ?string $code = null,
        ?Subordination $subordination = null,
        ?Subordination $coSubordination = null
    ): string;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18812683
     * @param string $departmentId
     * @param string|null $parentDepartmentId
     * @param string|null $name
     * @param string|null $code
     * @param Subordination|null $subordination
     * @param Subordination|null $coSubordination
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function editDepartment(
        string $departmentId,
        ?string $parentDepartmentId = null,
        ?string $name = null,
        ?string $code = null,
        ?Subordination $subordination = null,
        ?Subordination $coSubordination = null
    ): bool;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18808972
     * @param string $departmentId
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function deleteDepartment(string $departmentId): bool;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=24677937
     * @param null|array{
     *     departments: null|array<string>,
     *     groups: null|array<string>,
     *     pageSize: null|int,
     *     pageToken: null|string,
     *     logins: null|array<string>,
     *     emails: null|array<string>,
     *     status: null|int,
     *     workLeaveStatus: null|array{
     *          workLeaveReason: null|array<string>,
     *          startDate: null|string,
     *          endDate: null|string
     *     },
     * } $parameters
     * @return UsersPaginatedResponse
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function getUsersPaginated(?array $parameters = null): UsersPaginatedResponse;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18809020
     * @param null|array{
     *     departments: null|array<string>,
     *     groups: null|array<string>
     * } $parameters
     * @return array<UserProfile>
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function getUsers(?array $parameters = null): array;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18808978
     * @param string $userId
     * @return UserProfile
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function getUser(string $userId): UserProfile;

    /**
     * @param string $departmentId
     * @param string $login
     * @param array<UserProfileField> $fields
     * @param string|null $email
     * @param string|null $password
     * @param bool $sendLoginEmail
     * @param string|null $invitationMessage
     * @param bool $sendLoginSMS
     * @param string|null $invitationSMSMessage
     * @param string|null $role
     * @param string|null $roleId
     * @param array<string>|null $manageableDepartmentIds
     * @param array<string>|null $groups
     * @param array<Role>|null $roles
     * @return string
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
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
        ?array $roles = null,
    ): string;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18809047
     * @param string $userId
     * @param string|null $departmentId
     * @param array<UserProfileField>|null $fields
     * @param string|null $role
     * @param string|null $roleId
     * @param array<string>|null $manageableDepartmentIds
     * @param array<string>|null $groupIds
     * @param array<Role>|null $roles
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function editUser(
        string $userId,
        ?string $departmentId = null,
        ?array $fields = null,
        ?string $role = null,
        ?string $roleId = null,
        ?array $manageableDepartmentIds = null,
        ?array $groupIds = null,
        ?array $roles = null,
    ): bool;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18809054
     * @param string $userId
     * @param string $password
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function editUserPassword(string $userId, string $password): bool;

    /**
     * https://docs.ispring.ru/pages/viewpage.action?pageId=18809062
     * @param string $userId
     * @param int $status
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function editUserStatus(string $userId, int $status): bool;

    /**
     * @param string $userId
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function activateUser(string $userId): bool;

    /**
     * @param string $userId
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function deactivateUser(string $userId): bool;

    /**
     * @param string $userId
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function terminateUser(string $userId): bool;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=28299959
     * @param string $userId
     * @param Carbon $date
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function deactivateUserScheduled(string $userId, Carbon $date): bool;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=28299988
     * @param string $userId
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function deactivateUserScheduledCancel(string $userId): bool;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=71837279
     * @param string $userId
     * @param Carbon $date
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function terminateUserScheduled(string $userId, Carbon $date): bool;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=71837283
     * @param string $userId
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function terminateUserScheduledCancel(string $userId): bool;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=71833760
     * @param array<string> $userIds
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function massActivateUsers(array $userIds): bool;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=71833777
     * @param array<string> $userIds
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function massDeactivateUsers(array $userIds): bool;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=71837289
     * @param array<string> $userIds
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function massTerminateUsers(array $userIds): bool;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18808980
     * @param string $userId
     * @return bool
     *
     * @throws ConnectionException
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws PermissionDeniedException
     * @throws ISpringLearnHTTPException
     */
    public function deleteUser(string $userId): bool;
}