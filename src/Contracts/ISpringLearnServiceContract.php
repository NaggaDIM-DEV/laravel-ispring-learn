<?php

namespace NaggadimDev\LaravelIspringLearn\Contracts;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use NaggadimDev\LaravelIspringLearn\Responses\UsersPaginatedResponse;

interface ISpringLearnServiceContract
{
    public function getRequest(string $url, ?array $query = null): PromiseInterface|Response;

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
    public function usersPaginated(?array $parameters = null): UsersPaginatedResponse;
}