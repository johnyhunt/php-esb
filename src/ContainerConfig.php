<?php

declare(strict_types=1);

namespace ESB;

use ESB\Middleware\PostSuccessMiddleware;
use ESB\Middleware\ProcessingMiddleware;
use ESB\Middleware\SyncRecordsMiddleware;
use ESB\Middleware\TransportMiddleware;
use ESB\Middleware\ValidatorMiddleware;
use Psr\Container\ContainerInterface;

class ContainerConfig
{
    public function __invoke() : array
    {
        return [
            'validators' => [
                // Reserved key for custom validators, should implement ValidatorInterface
                // 'alias' => CustomValidator::class,
            ],

            Core::class => function(ContainerInterface $container) : Core
            {
                return new Core(
                    $container->get(ValidatorMiddleware::class),
                    $container->get(ProcessingMiddleware::class),
                    $container->get(TransportMiddleware::class),
                    $container->get(SyncRecordsMiddleware::class),
                    $container->get(PostSuccessMiddleware::class),
                );
            },
        ];
    }
}
