<?php

declare(strict_types=1);

namespace ESB;

use ESB\Auth\AuthServiceInterface;
use ESB\Exception\SetupException;
use ESB\Handlers\PostHandlerInterface;
use ESB\Handlers\QueueMessageHandler;
use ESB\Middleware\Core\PostSuccessMiddleware;
use ESB\Middleware\Core\ProcessingMiddleware;
use ESB\Middleware\Core\SyncRecordsMiddleware;
use ESB\Middleware\Core\TransportMiddleware;
use ESB\Middleware\Core\ValidatorMiddleware;
use ESB\Middleware\Queue\ErrorHandlerMiddleware;
use ESB\Middleware\Queue\RunCoreMiddleware;
use ESB\Service\AuthServicePool;
use ESB\Service\ClientPool;
use ESB\Service\CoreRunnersPool;
use ESB\Service\ValidatorsPool;
use ESB\Service\PostSuccessHandlersPool;
use ESB\Validation\ValidatorInterface;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TwigFunction;

use function getenv;

class ContainerConfig
{
    public function __invoke() : array
    {
        return [
            'validators' => [
                // Reserved key for custom validators, should implement ValidatorInterface
                // 'alias' => CustomValidator::class,
            ],
            'formatters' => [
                // Reserved key for twig function-helpers
                // alias => Invocable::class
            ],
            'post-success' => [
                // Reserved key for post success handlers
                // alias => MyPostSuccessHandler::class
            ],
            'post-error' => [
                // Reserved key for post error handlers
                // alias => MyPostErrorHandler::class
            ],
            'runner' => [
                // Reserved key for custom runners
                // alias => MyCustomRunner::class
            ],
            'auth' => [
                // Reserved key for auth services
                // alias => MyAuthService::class
            ],

            Core::class => function(ContainerInterface $container) : Core
            {
                // PostSuccessMiddleware is last, ValidatorMiddleware is first
                return new Core(
                    $container->get(PostSuccessMiddleware::class),
                    $container->get(SyncRecordsMiddleware::class),
                    $container->get(TransportMiddleware::class),
                    $container->get(ProcessingMiddleware::class),
                    $container->get(ValidatorMiddleware::class),
                );
            },

            CoreRunner::class => function(ContainerInterface $container) : CoreRunner
            {
                return new CoreRunner(
                    $container->get(Core::class),
                );
            },

            CoreRunnersPool::class => function(ContainerInterface $container) : CoreRunnersPool
            {
                $pool           = new CoreRunnersPool($container->get(CoreRunner::class));
                $definedRunners = $container->get('runner');

                foreach ($definedRunners as $alias => $runnerClass) {
                    $runner = $container->get($runnerClass);
                    if (! $runner instanceof CoreRunnerInterface) {
                        throw new SetupException('ESBHandler: runner config invalid');
                    }
                    $pool->add($alias, $runner);
                }

                return $pool;
            },

            Environment::class => function(ContainerInterface $container) : Environment
            {
                $twig = new Environment(new ArrayLoader(), [
                    'strict_variables' => false,
                    'autoescape' => false,
                ]);

                $definedFormatters = $container->get('formatters');

                foreach ($definedFormatters as $key => $formatterClass) {
                    $formatter = $container->get($formatterClass);
                    $twig->addFunction(new TwigFunction($key, $formatter(...)));
                }

                return $twig;
            },

            QueueMessageHandler::class => fn(ContainerInterface $container) => new QueueMessageHandler(
                $container->get(RunCoreMiddleware::class),
                $container->get(ErrorHandlerMiddleware::class),
            ),

            ClientPool::class => fn() => new ClientPool(),

            AuthServicePool::class => function(ContainerInterface $container) : AuthServicePool {
                $pool         = new AuthServicePool;
                $authServices = $container->get('auth');

                foreach ($authServices as $alias => $serviceClass) {
                    $service = $container->get($serviceClass);
                    if (! $service instanceof AuthServiceInterface) {
                        throw new SetupException('AuthServicePool: auth config invalid');
                    }
                    $pool->add($alias, $service);
                }

                return $pool;
            },

            ValidatorsPool::class => function(ContainerInterface $container) : ValidatorsPool
            {
                // Get list of defined validators
                $definedValidators = $container->get('validators');

                $pool = new ValidatorsPool;
                foreach ($definedValidators as $alias => $validatorClass) {
                    $validator = $container->get($validatorClass);
                    if (! $validator instanceof ValidatorInterface) {
                        throw new SetupException('ValidatorMiddleware: custom validator config invalid');
                    }
                    $pool->add($alias, $validator);
                }

                return $pool;
            },

            PostSuccessHandlersPool::class => function(ContainerInterface $container) : PostSuccessHandlersPool {
                $pool = new PostSuccessHandlersPool();
                // Get list of defined post success handlers
                $definedHandlers = $container->get('post-success');

                // Prepare list with all classes
                foreach ($definedHandlers as $alias => $handlerClass) {
                    $containerHandler = $container->get($handlerClass);
                    if (! $containerHandler instanceof PostHandlerInterface) {
                        throw new SetupException('PostSuccessMiddleware: custom handler config invalid');
                    }
                    $pool->add($alias, $containerHandler);
                }

                return $pool;
            },
        ];
    }
}
