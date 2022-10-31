<?php

declare(strict_types=1);

namespace ESB;

use ESB\Exception\ESBException;
use ESB\Handlers\HTTP\ESBHandler;
use ESB\Handlers\PostHandlerInterface;
use ESB\Middleware\PostSuccessMiddleware;
use ESB\Middleware\ProcessingMiddleware;
use ESB\Middleware\SyncRecordsMiddleware;
use ESB\Middleware\TransportMiddleware;
use ESB\Middleware\ValidatorMiddleware;
use ESB\Repository\RouteRepositoryInterface;
use ESB\Validation\ValidatorInterface;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\EscaperExtension;
use Twig\Lexer;
use Twig\Loader\ArrayLoader;
use Twig\TwigFunction;

use function sprintf;
use function var_dump;

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

            ESBHandler::class => function(ContainerInterface $container) : ESBHandler
            {
                $definedRunners = $container->get('runner');

                $runners = [];

                foreach ($definedRunners as $alias => $runnerClass) {
                    $runner = $container->get($runnerClass);
                    if (! $runner instanceof CoreRunnerInterface) {
                        throw new ESBException('ESBHandler: runner config invalid');
                    }
                    $runners[$alias] = $runner;
                }

                $runners[CoreRunner::class] = $container->get(CoreRunner::class);

                return new ESBHandler(
                    $runners,
                    $container->get(RouteRepositoryInterface::class)
                );
            },

            ValidatorMiddleware::class => function(ContainerInterface $container) : ValidatorMiddleware
            {
                // Get list of defined validators
                $definedValidators = $container->get('validators');

                // List with custom ValidatorInterface class objects
                $customContainerValidators = [];

                foreach ($definedValidators as $alias => $validatorClass) {
                    $validator = $container->get($validatorClass);
                    if (! $validator instanceof ValidatorInterface) {
                        throw new ESBException('ValidatorMiddleware: custom validator config invalid');
                    }
                    $customContainerValidators[$alias] = $validator;
                }

                return new ValidatorMiddleware($customContainerValidators);
            },

            Environment::class => function(ContainerInterface $container) : Environment
            {
                $twig = new Environment(new ArrayLoader());

                $definedFormatters = $container->get('formatters');

                foreach ($definedFormatters as $key => $formatterClass) {
                    $formatter = $container->get($formatterClass);
                    $twig->addFunction(new TwigFunction($key, $formatter(...)));
                }
                /** TODO need also spaceless for json */
                $twig->getExtension(EscaperExtension::class)->setEscaper('json_string', fn(Environment $twig, string $value) => sprintf('"%s"', $value));

                return $twig;
            },

            PostSuccessMiddleware::class => function(ContainerInterface $container) : PostSuccessMiddleware
            {
                // Get list of defined post success handlers
                $definedHandlers = $container->get('post-success');

                // List with custom PostHandlerInterface class objects
                $customContainerHandlers = [];

                // Prepare list with all classes
                foreach ($definedHandlers as $alias => $handlerClass) {
                    $containerHandler = $container->get($handlerClass);
                    if (! $containerHandler instanceof PostHandlerInterface) {
                        throw new ESBException('PostSuccessMiddleware: custom handler config invalid');
                    }
                    $customContainerHandlers[$alias] = $containerHandler;
                }

                return new PostSuccessMiddleware($customContainerHandlers);
            }
        ];
    }
}
