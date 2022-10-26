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

class ContainerConfig
{
    public function __invoke() : array
    {
        return [
            'validators' => [
                // Reserved key for custom validators, should implement ValidatorInterface
                // 'alias' => CustomValidator::class,
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
        ];
    }
}
