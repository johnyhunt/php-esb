<?php

use DI\ContainerBuilder;
use ESB\ContainerConfig;
use Opsway\ESB\ContainerConfig as RouteConfig;
use Opsway\ESB\Service\RouteProviderInterface;
use Slim\Factory\AppFactory;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// Set up settings
$containerBuilder->addDefinitions((new RouteConfig())(), (new ContainerConfig())());

// Build PHP-DI Container instance
$container = $containerBuilder->build();

if (getenv('APPLICATION_TYPE') == 'server')
{
    // Create App instance
    $app = AppFactory::create();
    (require __DIR__ . '/routes.php')($app, $container->get(RouteProviderInterface::class));
    (require __DIR__ . '/middleware.php')($app);
}

if (getenv('APPLICATION_TYPE') == 'consumer') {
    $application = new Application();
    foreach ($container->get('settings')['commands'] as $class) {
        try {
            $application->add($container->get($class));
        } catch (Throwable $e) {
            echo sprintf("%s", $e->getMessage());
            echo "\n";
            echo json_encode($e->getTrace());
        }

    }
}

return $app;