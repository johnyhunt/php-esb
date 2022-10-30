<?php

declare(strict_types=1);

namespace Example;

use ESB\Entity\IntegrationSystem;
use ESB\Entity\Route;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\OutputDataMap;
use ESB\Entity\VO\PostHandler;
use ESB\Entity\VO\SyncTable;
use ESB\Exception\ESBException;
use ESB\Repository\RouteRepositoryInterface;
use Example\Assembler\InputDataMapAssembler;
use Example\Service\DsnInterpreterInterface;

use function file_get_contents;
use function implode;
use function json_decode;
use function var_dump;

class RouteRepository implements RouteRepositoryInterface
{
    private const __FIXTURES__ = __DIR__ . '/../fixtures/';

    /** @psalm-var array<string, Route>  */
    private array $routes;

    public function __construct(private readonly DsnInterpreterInterface $dsnInterpreter)
    {
        $routes = [
            new Route(
                id: 'id_0',
                name: 'route_1',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP','GET','/v1/empty-test'])),
                fromSystemData: new InputDataMap(),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP','POST','google.com'])),
                toSystemData: new OutputDataMap(),
                syncTable: null,
                postSuccessHandlers: [new PostHandler(name: 'my-post-handler')]
            ),
            new Route(
                id: 'id_1',
                name: 'route_1',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP','GET','/v1/test'])),
                fromSystemData: (new InputDataMapAssembler())(json_decode(file_get_contents(self::__FIXTURES__ . 'validationRules1.json'), true)),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP','POST','google.com'])),
                toSystemData: new OutputDataMap(file_get_contents(self::__FIXTURES__ .  'template1.xml.twig')),
                syncTable: null
            ),
            new Route(
                id: 'id_2',
                name: 'route_2',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP','POST','/v1/test-post'])),
                fromSystemData: (new InputDataMapAssembler())(json_decode(file_get_contents(self::__FIXTURES__  . 'validationRules2.json'), true)),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP','POST','google.com'])),
                toSystemData: new OutputDataMap(file_get_contents(self::__FIXTURES__  . 'template2.json.twig')),
                syncTable: new SyncTable('sync_1', ['id' => 'customer.id', 'hash' => ['customer', 'brands']], ['id' => 'customer.id'], true, true),
            ),
            new Route(
                id: 'id_3',
                name: 'route_3',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['pubsub','example','test-action'])),
                fromSystemData: new InputDataMap(),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP','POST','google.com'])),
                toSystemData: new OutputDataMap(''),
                syncTable: null,
            ),
        ];

        foreach ($routes as $route) {
            $this->routes[$route->fromSystemDsn()->dsn()] = $route;
        }
    }

    public function get(string $key) : Route
    {
        return $this->routes[$key] ?? throw new ESBException('Unknown route');
    }

    public function loadAll() : array
    {
        return $this->routes;
    }
}
