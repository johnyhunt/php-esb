<?php

declare(strict_types=1);

namespace Example;

use ESB\Entity\IntegrationSystem;
use ESB\Entity\Route;
use ESB\Entity\VO\InputDataMap;
use ESB\Exception\ESBException;
use ESB\Repository\RouteRepositoryInterface;
use Example\Assembler\InputDataMapAssembler;
use Example\Service\DsnInterpreterInterface;

class RouteRepository implements RouteRepositoryInterface
{
    /** @psalm-var array<string, Route>  */
    private array $routes;

    public function __construct(private readonly DsnInterpreterInterface $dsnInterpreter)
    {
        $routes = [
            new Route(
                id: 'id_1',
                name: 'route_1',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)('HTTP:GET:/v1/test'),
                fromSystemData: new InputDataMap(),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)('HTTP:POST:google.com'),
            ),
            new Route(
                id: 'id_2',
                name: 'route_2',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)('HTTP:POST:/v1/test-post'),
                fromSystemData: (new InputDataMapAssembler())(
                    [
                        'type'       => 'object',
                        'required'   => true,
                        'example'    => '',
                        'items'      => null,
                        'validators' => null,
                        'properties' => [
                            'customer' => [
                                'type'       => 'object',
                                'required'   => true,
                                'example'    => '',
                                'items'      => null,
                                'properties' => [
                                    'id' => [
                                        'type'       => 'int',
                                        'required'   => true,
                                        'example'    => '585781',
                                        'items'      => null,
                                        'properties' => null,
                                        'validators' => [['assert' => 'min', 'params' => ['minValue' => 1]]],
                                    ],
                                    'person' => [
                                        'type'       => 'object',
                                        'required'   => false,
                                        'example'    => '',
                                        'items'      => null,
                                        'properties' => [
                                            'first_name' => [
                                                'type'       => 'string',
                                                'required'   => true,
                                                'example'    => 'Test',
                                                'items'      => null,
                                                'properties' => null,
                                                'validators' => null,
                                            ],
                                            'last_name'  => [
                                                'type'       => 'string',
                                                'required'   => true,
                                                'example'    => '',
                                                'items'      => null,
                                                'properties' => null,
                                                'validators' => null,
                                            ],
                                        ],
                                        'validators' => null,
                                    ],
                                    'organization' => [
                                        'type'       => 'object',
                                        'required'   => false,
                                        'example'    => '',
                                        'items'      => null,
                                        'properties' => [
                                            'contact_name' => [
                                                'type'       => 'string',
                                                'required'   => true,
                                                'example'    => 'Test',
                                                'items'      => null,
                                                'properties' => null,
                                                'validators' => null,
                                            ],
                                            'company'  => [
                                                'type'       => 'string',
                                                'required'   => true,
                                                'example'    => '',
                                                'items'      => null,
                                                'properties' => null,
                                                'validators' => null,
                                            ]
                                        ],
                                        'validators' => null,
                                    ],
                                ],
                                'validators' => [['assert' => 'oneOf', 'params' => ['person', 'organization']]],
                            ],
                            'brands' => [
                                'type'       => 'array',
                                'required'   => true,
                                'example'    => '',
                                'items'      => [
                                    'type'       => 'object',
                                    'required'   => true,
                                    'example'    => '',
                                    'items'      => null,
                                    'properties' => [
                                        'id'   => [
                                            'type'       => 'int',
                                            'required'   => true,
                                            'example'    => '',
                                            'items'      => null,
                                            'properties' => null,
                                            'validators' => null,
                                        ],
                                        'name' => [
                                            'type'       => 'string',
                                            'required'   => true,
                                            'example'    => '',
                                            'items'      => null,
                                            'properties' => null,
                                            'validators' => null,
                                        ],
                                    ],
                                    'validators' => null,
                                ],
                                'properties' => null,
                                'validators' => null,
                            ]
                        ],
                    ]
                ),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)('HTTP:POST:google.com'),
            ),
            new Route(
                id: 'id_3',
                name: 'route_3',
                fromSystem: new IntegrationSystem('system_1'),
                fromSystemDsn: ($this->dsnInterpreter)('pubsub:example:test-action'),
                fromSystemData: new InputDataMap(),
                toSystem: new IntegrationSystem('system_2'),
                toSystemDsn: ($this->dsnInterpreter)('HTTP:POST:google.com'),
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
