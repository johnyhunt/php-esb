<?php

declare(strict_types=1);

namespace Example;

use ESB\CoreHandlerInterface;
use ESB\DTO\RouteData;
use ESB\Entity\Route;
use ESB\Entity\VO\SyncTable;
use ESB\Repository\RouteRepositoryInterface;
use ESB\Repository\SyncTableRepositoryInterface;
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

            RouteRepositoryInterface::class     => fn(ContainerInterface $container) => $container->get(RouteRepository::class),
            DsnInterpreterInterface::class      => new DsnInterpreter(),
            SyncTableRepositoryInterface::class => function() : SyncTableRepositoryInterface
            {
                return new class () implements SyncTableRepositoryInterface {
                    public function wasSynced(SyncTable $syncTable) : bool
                    {
                        return false;
                    }

                    public function store(RouteData $data, SyncTable $syncTable) : void
                    {
                    }
                };
            },
        ];
    }
}
