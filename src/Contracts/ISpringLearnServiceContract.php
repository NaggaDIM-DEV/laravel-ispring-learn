<?php

namespace NaggadimDev\LaravelIspringLearn\Contracts;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use NaggadimDev\LaravelIspringLearn\DTO\Department;
use NaggadimDev\LaravelIspringLearn\DTO\Subordination;
use NaggadimDev\LaravelIspringLearn\DTO\UserProfile;
use NaggadimDev\LaravelIspringLearn\Responses\DepartmentsPaginatedResponse;
use NaggadimDev\LaravelIspringLearn\Responses\UsersPaginatedResponse;

interface ISpringLearnServiceContract
{
    public function getRequest(string $url, ?array $query = null): PromiseInterface|Response;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=24677928
     * @param null|array{
     *     pageSize: null|int,
     *     pageToken: null|string,
     * } $parameters
     * @return DepartmentsPaginatedResponse
     */
    public function getDepartmentsPaginated(?array $parameters = null): DepartmentsPaginatedResponse;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18808974
     * @return array<Department>
     */
    public function getDepartments(): array;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18809544
     * @param string $departmentId
     * @return Department
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
     */
    public function getUsersPaginated(?array $parameters = null): UsersPaginatedResponse;

    /**
     * @reference https://docs.ispring.ru/pages/viewpage.action?pageId=18808978
     * @param string $userId
     * @return UserProfile
     */
    public function getUser(string $userId): UserProfile;
}