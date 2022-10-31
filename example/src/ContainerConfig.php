<?php

declare(strict_types=1);

namespace Example;

use ESB\Core;
use ESB\Repository\RouteRepositoryInterface;
use ESB\Repository\SyncRecordRepositoryInterface;
use Example\Formatter\SellerMap;
use Example\Handlers\Success\MyPostSuccessHandler;
use Example\Runner\MyCustomRunner;
use Example\Service\DsnInterpreter;
use Example\Service\DsnInterpreterInterface;
use Example\Validator\OneOf;
use Psr\Container\ContainerInterface;

class ContainerConfig
{
    public function  __invoke() : array
    {
        return [
            'validators' => [
                'oneOf' => OneOf::class,
            ],
            'formatters' => [
                'sellerMap' => SellerMap::class,
            ],
            'post-success' => [
                'my-post-handler' => MyPostSuccessHandler::class
            ],
            'runner' => [
                'my-runner' => MyCustomRunner::class,
            ],

            RouteRepositoryInterface::class => fn(ContainerInterface $container) => $container->get(RouteRepository::class),

            DsnInterpreterInterface::class => new DsnInterpreter(),

            SyncRecordRepositoryInterface::class => new SyncRecordRepository(),

            MyCustomRunner::class => fn(ContainerInterface $container) => new MyCustomRunner($container->get(Core::class)),
        ];
    }
}
