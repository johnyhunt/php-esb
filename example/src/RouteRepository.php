<?php

declare(strict_types=1);

namespace Example;

use ESB\Assembler\DsnInterpreterInterface;
use ESB\Assembler\InputDataMapAssembler;
use ESB\Entity\IntegrationSystem;
use ESB\Entity\Route;
use ESB\Entity\SyncTable;
use ESB\Entity\VO\AbstractDSN;
use ESB\Entity\VO\AuthMap;
use ESB\Entity\VO\InputDataMap;
use ESB\Entity\VO\SyncSettings;
use ESB\Entity\VO\TargetRequestMap;
use ESB\Entity\VO\PostHandler;
use ESB\Repository\RouteRepositoryInterface;
use Exception;

use function file_get_contents;
use function implode;
use function json_decode;

class RouteRepository implements RouteRepositoryInterface
{
    private const __FIXTURES__ = __DIR__ . '/../fixtures/';

    /** @psalm-var array<string, Route>  */
    private array $routes;

    public function __construct(private readonly DsnInterpreterInterface $dsnInterpreter)
    {
        $routes = [
            new Route(
                name: 'route_0',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'GET', '/v1/empty'])),
                fromSystemData: new InputDataMap(),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'GET', 'https://catfact.ninja/fact'])),
                toSystemData: new TargetRequestMap(responseFormat: 'json'),
                syncSettings: null,
                postSuccessHandlers: null,
                postErrorHandlers: null,
                customRunner: null
            ),
            new Route(
                name: 'route_1',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'POST', '/v1/test123'])),
                fromSystemData: (new InputDataMapAssembler())(json_decode(file_get_contents(self::__FIXTURES__ . 'test.json'), true)),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'GET', 'app:8080/test'])),
                toSystemData: new TargetRequestMap(),
                syncSettings: new SyncSettings(new SyncTable('example'), 'body.id', 'response.soapenv:Envelope.Item.ItemProduct.ProductInternalID', true, true),
                postSuccessHandlers: [new PostHandler(name: 'my-post-handler')],
                customRunner: 'my-runner'
            ),
            new Route(
                name: 'route_2',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'GET', '/v1/test'])),
                fromSystemData: (new InputDataMapAssembler())(json_decode(file_get_contents(self::__FIXTURES__ . 'validationRules1.json'), true)),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'POST', 'google.com'])),
                toSystemData: new TargetRequestMap(template: file_get_contents(self::__FIXTURES__ . 'template1.xml.twig')),
            ),
            new Route(
                name: 'route_3',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'POST', '/v1/test-post'])),
                fromSystemData: (new InputDataMapAssembler())(json_decode(file_get_contents(self::__FIXTURES__ . 'validationRules2.json'), true)),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'POST', 'google.com'])),
                toSystemData: new TargetRequestMap(
                    template: file_get_contents(self::__FIXTURES__ . 'template2.json.twig'),
                    responseFormat: 'json',
                    auth: new AuthMap('JsonAuthService', [
                        'data'        => ['login' => '123', 'password' => '345'],
                        'dsn'         => implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'POST', 'google.com']),
                        'headers'     => ['Content-Type' => 'application/json'],
                        'token'       => 'response.body.session',
                        'output-name' => 'token',
                    ]),
                ),
                syncSettings: new SyncSettings(new SyncTable('example'), 'body.id', 'data.externalId', true, false),
            ),
            new Route(
                name: 'route_4',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['pubsub', 'example', 'example.sub', 'test-action'])),
                fromSystemData: new InputDataMap(),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)(implode(AbstractDSN::DSN_SEPARATOR, ['HTTP', 'POST', 'google.com'])),
                toSystemData: new TargetRequestMap(template: ''),
            ),
        ];

        foreach ($routes as $route) {
            $this->routes[$route->name()] = $route;
        }
    }

    public function get(string $fromSystemDsn) : Route
    {
        return $this->routes[$fromSystemDsn] ?? throw new Exception('Unknown route');
    }

    public function getByName(string $name) : Route
    {
        throw new Exception(sprintf('%s route wasn`t found', $name));
    }

    public function loadAll() : array
    {
        return $this->routes;
    }

    public function store(Route $route) : void
    {
        // TODO: Implement store() method.
    }

    public function delete(Route $route) : void
    {
        // TODO: Implement delete() method.
    }
}
