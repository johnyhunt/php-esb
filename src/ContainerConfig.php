<?php

declare(strict_types=1);

namespace ESB;

use ESB\Handlers\ESBHandler;
use ESB\Handlers\ESBHandlerInterface;
use ESB\Middleware\ValidatorMiddleware;
use ESB\Service\DsnInterpreter;
use ESB\Service\DsnInterpreterInterface;
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

            CoreInterface::class => function(ContainerInterface $container) : CoreInterface
            {
                /** @psalm-var Core $coreHandler */
                $coreHandler = $container->get(Core::class);
                $coreHandler->setUpMiddlewares(
                    ValidatorMiddleware::class,
                );

                return $coreHandler;
            },

            ESBHandlerInterface::class => function(ContainerInterface $container) : ESBHandlerInterface {
                $settings = $container->get('settings');

                return new ESBHandler(
                    $container->get(RouteProviderInterface::class),
                    $container->get(CoreInterface::class),
                    $settings['routingBasePath']);
            },
        ];
    }
}
