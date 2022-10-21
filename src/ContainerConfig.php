<?php

declare(strict_types=1);

namespace ESB;

use ESB\Handlers\ESBHandler;
use ESB\Handlers\EsbHandlerInterface;
use ESB\Middleware\ValidatorMiddleware;
use ESB\Service\DsnInterpreter;
use ESB\Service\DsnInterpreterInterface;
use ESB\Service\RouteProvider;
use ESB\Service\RouteProviderInterface;
use ESB\Service\ServerAppSetup;
use ESB\Service\ServerAppSetupInterface;
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

            RouteProviderInterface::class  =>fn(ContainerInterface $container) => $container->get(RouteProvider::class),

            ServerAppSetupInterface::class => function(ContainerInterface $container) : ServerAppSetupInterface {
                $settings = $container->get('settings');

                return new ServerAppSetup($container->get(RouteProviderInterface::class), $settings['routingBasePath']);
            },

            ESBCoreInterface::class => function(ContainerInterface $container) : ESBCoreInterface
            {
                /** @psalm-var ESBCore $coreHandler */
                $coreHandler = $container->get(ESBCore::class);
                $coreHandler->setUpMiddlewares(
                    ValidatorMiddleware::class,
                );

                return $coreHandler;
            },

            EsbHandlerInterface::class => function(ContainerInterface $container) : EsbHandlerInterface {
                $settings = $container->get('settings');

                return new ESBHandler(
                    $container->get(RouteProviderInterface::class),
                    $container->get(ESBCoreInterface::class),
                    $settings['routingBasePath']);
            },
        ];
    }
}
