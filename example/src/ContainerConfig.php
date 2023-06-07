<?php

declare(strict_types=1);

namespace Example;

use ESB\Assembler\DsnInterpreterInterface;
use ESB\Repository\RouteRepositoryInterface;
use ESB\Repository\SyncRecordRepositoryInterface;
use ESB\Service\ClientPool;
use Example\Clients\HttpClient;
use Example\Formatter\CustomFormatter;
use Example\Handlers\Success\MyPostSuccessHandler;
use Example\Runner\MyCustomRunner;
use Example\Service\DsnInterpreter;
use Example\Validation\AssertValidator;
use Example\Validation\OneOf;
use Psr\Container\ContainerInterface;

class ContainerConfig
{
    public function  __invoke() : array
    {
        return [
            'validators'   => [
                'oneOf'           => OneOf::class,
                'assertValidator' => AssertValidator::class,
            ],
            'formatters'   => [
                'customFormatter' => CustomFormatter::class,
            ],
            'post-success' => [
                'my-post-handler' => MyPostSuccessHandler::class
            ],
            'runner'       => [
                'my-runner' => MyCustomRunner::class,
            ],
            'auth'         => [
                // 'jsonAuthService' => JsonAuthService::class,
            ],

            RouteRepositoryInterface::class => fn(ContainerInterface $container) => $container->get(RouteRepository::class),

            DsnInterpreterInterface::class => new DsnInterpreter(),

            SyncRecordRepositoryInterface::class => new SyncRecordRepository(),

            ClientPool::class => fn(ContainerInterface $container) => new ClientPool($container->get(HttpClient::class)),
        ];
    }
}
