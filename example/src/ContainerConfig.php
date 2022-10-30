<?php

declare(strict_types=1);

namespace Example;

use ESB\Repository\RouteRepositoryInterface;
use Example\Formatter\SellerMap;
use Example\Handlers\Success\MyPostSuccessHandler;
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

            RouteRepositoryInterface::class => fn(ContainerInterface $container) => $container->get(RouteRepository::class),
            DsnInterpreterInterface::class  => new DsnInterpreter(),
        ];
    }
}
