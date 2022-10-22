<?php

declare(strict_types=1);

namespace ESB;

use ESB\Handlers\ESBHandler;
use ESB\Handlers\ESBHandlerInterface;
use ESB\Middleware\PostSuccessMiddleware;
use ESB\Middleware\ProcessingMiddleware;
use ESB\Middleware\SyncRecordsMiddleware;
use ESB\Middleware\TransportMiddleware;
use ESB\Middleware\ValidatorMiddleware;
use Example\Service\DsnInterpreter;
use Example\Service\DsnInterpreterInterface;
use Psr\Container\ContainerInterface;

class ContainerConfig
{
    public function __invoke() : array
    {
        return [
            'settings'   => [
                'routingBasePath' => '/middleware'
            ],
            'validators' => [
                // Reserved key for custom validators, should implement ValidatorInterface
                // 'alias' => CustomValidator::class,
            ],
            DsnInterpreterInterface::class => new DsnInterpreter(),

            ServerAppSetup::class => function(ContainerInterface $container) : ServerAppSetup {
                $settings = $container->get('settings');

                return new ServerAppSetup($container->get(RouteProviderInterface::class), $settings['routingBasePath']);
            },

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

            ESBHandlerInterface::class => function(ContainerInterface $container) : ESBHandlerInterface {
                $settings = $container->get('settings');

                return new ESBHandler(
                    $container->get(RouteProviderInterface::class),
                    $container->get(Core::class),
                    $settings['routingBasePath']);
            },
        ];
    }
}
