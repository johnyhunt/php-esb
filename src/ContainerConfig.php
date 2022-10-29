<?php

declare(strict_types=1);

namespace ESB;

use ESB\Exception\ESBException;
use ESB\Middleware\PostSuccessMiddleware;
use ESB\Middleware\ProcessingMiddleware;
use ESB\Middleware\SyncRecordsMiddleware;
use ESB\Middleware\TransportMiddleware;
use ESB\Middleware\ValidatorMiddleware;
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

            ValidatorMiddleware::class => function(ContainerInterface $container)
            {
                {
                    $definedValidators   = $container->get('validators');
                    $validatorMiddleware = new ValidatorMiddleware();
                    foreach ($definedValidators as $key => $validatorClass) {
                        $validator = $container->get($validatorClass);
                        if (! $validator instanceof ValidatorInterface) {
                            throw new ESBException('ValidatorMiddleware: custom validator config invalid');
                        }
                        $validatorMiddleware->addCustomValidator($key, $validator);
                    }

                    return $validatorMiddleware;
                }
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
            }
        ];
    }
}
