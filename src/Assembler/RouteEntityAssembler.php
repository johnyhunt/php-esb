<?php

declare(strict_types=1);

namespace ESB\Assembler;

use ESB\Entity\IntegrationSystem;
use ESB\Entity\Route;
use ESB\Entity\SyncTable;
use ESB\Entity\VO\PostHandler;
use ESB\Entity\VO\SyncSettings;
use ESB\Entity\VO\TargetRequestMap;

use function array_map;

/**
 * @psalm-type integrationSystem = array{
 *      code: string
 * }
 * @psalm-type authMap = array{
 *      serviceAlias: string,
 *      settings: string[]
 * }
 * @psalm-type targetRequestMap = array{
 *      headers: string[],
 *      template: string|null,
 *      auth: authMap|null,
 *      responseFormat: string,
 * }
 * @psalm-type syncTable = array{
 *      tableName: string
 * }
 * @psalm-type syncSettings = array{
 *      pkPath: string,
 *      responsePkPath: string,
 *      syncOnExist: string,
 *      syncOnChange: string,
 *      updateRouteId: string|null,
 * }
 * @psalm-type inputRow = array{
 *      name: string,
 *      description: string|null,
 *      fromSystem: integrationSystem,
 *      fromSystem_dsn: string,
 *      fromSystemData: array,
 *      toSystem: integrationSystem,
 *      toSystemDsn: string,
 *      toSystemData: targetRequestMap,
 *      syncTable: syncTable|null,
 *      syncSettings: syncSettings|null,
 *      postSuccessHandlers: string[]|null,
 *      customRunner: string|null,
 * }
 */
class RouteEntityAssembler
{
    public function __construct(
        private readonly InputDataMapAssembler $inputDataMapAssembler,
        private readonly DsnInterpreterInterface $dsnInterpreter,
        private readonly SyncSettingsAssembler $syncSettingsAssembler,
        private readonly ToSystemDataAssembler $toSystemDataAssembler,
    ) {
    }

    /** @psalm-param array<array-key, inputRow> $routes
     * @psalm-return array<array-key, Route>
     */
    public function __invoke(array $routes) : array
    {
        return array_map(fn(array $route) => $this->buildRoute($route), $routes);
    }

    /** @psalm-param inputRow $route */
    public function buildRoute(array $route) : Route
    {
        return new Route(
            name: $route['name'],
            fromSystem: new IntegrationSystem(...$route['fromSystem']),
            fromSystemDsn: ($this->dsnInterpreter)($route['fromSystemDsn']),
            fromSystemData: ($this->inputDataMapAssembler)($route['fromSystemData']),
            toSystem: new IntegrationSystem(...$route['toSystem']),
            toSystemDsn: ($this->dsnInterpreter)($route['toSystemDsn']),
            toSystemData: ($this->toSystemDataAssembler)($route['toSystemData']),
            syncSettings: ($this->syncSettingsAssembler)($route['syncTable'], $route['syncSettings']),
            postSuccessHandlers: $route['postSuccessHandlers'] ? array_map(fn(string $name) => new PostHandler($name), $route['postSuccessHandlers']) : null,
            customRunner: $route['customRunner'],
            description: $route['description'],
        );
    }
}
