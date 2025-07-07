<?php

namespace NaggadimDev\LaravelIspringLearn\Exceptions;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class ISpringLearnHTTPException extends ISpringLearnException
{
    const DEFAULT_MESSAGE = 'ISpringLearn HTTP Exception';


    public function __construct(PromiseInterface|Response $response)
    {
        $content = $response->json() ?? [];
        parent::__construct(
            message: $content['message'] ?? static::DEFAULT_MESSAGE,
            code: $response->status(),
            previous: $response->toException()
        );

        if(config('ispring-learn.logging.enabled', false)) {
            Log::build(config('ispring-learn.logging.channel'))->error(static::DEFAULT_MESSAGE, [
                'message'   => $content['message'] ?? static::DEFAULT_MESSAGE,
                'code'      => $response->status(),

                'response'  => $response->body(),
            ]);
        }
    }
}