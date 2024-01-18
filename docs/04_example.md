# Usage and examples

## CRUD

Library has build-in engine for load routes through http-CRUD([RouteCRUDHandler](../src/Handlers/HTTP/RouteCRUDHandler.php)).
CRUD is available on predefined URI:
 - **GET /route** - list
 - **GET /route/{routeName}** - read
 - **POST /route** - create. You have to pass JSON config of Route to API, will make validation and will check handlers-runners
on existing
 - **PUT /route** - update. Works the same as `create`
 - **DELETE /route/{routeName}** - delete

## Entrypoints

Library has **2 build-in entrypoints**(wrappers) to run **Core**. Both are injected via interface. You
can customize them by redefining in container:
- [ESBHandlerInterface](../src/Handlers/HTTP/ESBHandlerInterface.php) wrapper for
HTTP. Build-in implementation [ESBHandler](../src/Handlers/HTTP/ESBHandler.php) for **Slim App**
- [QueueMessageHandlerInterface](../src/Handlers/QueueMessageHandlerInterface.php) wrapper to run Core within
queue. [QueueMessageHandler](../src/Handlers/QueueMessageHandler.php)

## Configuration
- [ContainerConfig](../src/ContainerConfig.php) - container configuration. You can mixin library configuration and yours like 
in example below:
```php
<?php

use YourContainerConfig;
use ESB\ContainerConfig as LibContainer;
use DI\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// Set up settings
$containerBuilder->addDefinitions((new YourContainerConfig())() + (new LibContainer())());

// Build PHP-DI Container instance
return $containerBuilder->build();
```
- Slim App setup [ServerAppSetup](../src/ServerAppSetup.php). Routes are injected to Slim routing in ServerAppSetup::setupRoutes
You can extend and redefine `ServerAppSetup` by our own, for example to add some extra middlewares to Slim App.
```php
<?php

use YourExtendingServerAppSetup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$container = require 'bootstrap.php';

// Create App instance
$app = $container->get(App::class);

/** php-esb SLIM::APP init **/
$container->get(YourExtendingServerAppSetup::class)($app);

$app->get('/hello', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
    $response->getBody()->write("Hello world test!");

    return $response;
});

return $app;
```

## Store and load
Library require you to implement 2 interfaces:
- [RouteRepositoryInterface](../src/Repository/RouteRepositoryInterface.php) - to store and load routes
- [SyncRecordRepositoryInterface](../src/Repository/SyncRecordRepositoryInterface.php) - to store records, for preventing duplicate-calls

## Authorization

Auth services are using by `TransportMiddleware` to authorize request before send, due to **Route::TargetRequestMap::auth**

Example:

```php
<?php

declare(strict_types=1);

namespace Example\Auth;

use ESB\Auth\AuthServiceInterface;
use ESB\DTO\TargetRequest;
use ESB\Entity\IntegrationSystem;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;
use RuntimeException;

use function getenv;
use function json_encode;

class AuthExampleService implements AuthServiceInterface
{
    private GuzzleClient $client;

    public function __construct()
    {
        $this->client = new GuzzleClient();
    }

    public function authenticate(TargetRequest $targetRequest, IntegrationSystem $integrationSystem, array $settings) : void
    {
        $user     = getenv('USER_NAME') ?: throw new RuntimeException('USER_NAME env non been set');
        $password = getenv('USER_PASSWORD') ?: throw new RuntimeException('USER_PASSWORD env non been set');
        $host     = $integrationSystem->hosts()['auth'];

        $response = $this->client->request(
            'POST',
            (string) Utils::uriFor('path-to-auth')->withHost($host)->withScheme('https'),
            [
                RequestOptions::BODY => json_encode(['user' => $user, 'password' => $password]),
            ]
        )->getBody()->getContents();

        $targetRequest->headers += ["Authorization" => "Bearer {$response['token']}"];
    }
}
```

## Clients

Clients are using by `TransportMiddleware` to send data by **Route::toSystemDsn** config

Example:

```php
<?php

declare(strict_types=1);

namespace Example\Clients;

use Example\Entity\VO\PubSubDSN;
use Example\Queue\PubSub\PubSubFactory;
use Example\Queue\PubSub\PubSubProducerConfig;
use Example\Client\EsbClientInterface;
use ESB\DTO\Message\Envelope;
use ESB\DTO\Message\Message;
use ESB\DTO\TargetRequest;
use ESB\DTO\TargetResponse;
use ESB\Entity\IntegrationSystem;
use ESB\Entity\VO\AbstractDSN;
use Psr\Log\LoggerInterface;
use Lib\Utils\Units;
use Psr\Log\LogLevel;
use RuntimeException;

use function microtime;
use function random_int;

class PubSubClient implements EsbClientInterface
{
    public function __construct(private readonly PubSubFactory $factory, private readonly ESBLoggerInterface $loggerService)
    {
    }

    public function send(AbstractDSN $dsn, TargetRequest $targetRequest, IntegrationSystem $targetSystem, string $responseFormat) : TargetResponse
    {
        if (! $dsn instanceof PubSubDSN) {
            throw new RuntimeException('PubSubClient expects dsn been PubSubDSN instance');
        }
        $producer  = $this->factory->producer(new PubSubProducerConfig($dsn->pushTopic()));
        $start     = microtime(true);
        $result    = $producer->send(
            new Envelope(new Message($targetRequest->body, $dsn->action, ['id' => (string) random_int(1, 9999)] + $targetRequest->headers))
        );
        $spentTime = microtime(true) - $start;
        $this->loggerService->log(LogLevel::INFO, 'PubSub request:', ['body' => $targetRequest->body, 'action' => $dsn->action, 'headers' => $targetRequest->headers]);

        return new TargetResponse($result, (int) ($spentTime * 1000));
    }

    public function dsnMatchClass() : string
    {
        return PubSubDSN::class;
    }
}
```

## Route config

To pass **Route1** config to **POST /route**,
**postSuccessHandlerExample**, **customRunnerExample** and **authServiceExample** should be defined in container

```php
        'auth' => [
            'authServiceExample' => AuthServiceExample::class,
        ],
        'post-success' => [
            'postSuccessHandlerExample' => PostSuccessHandlerExample::class,
        ],
        'runner' => [
            'customRunnerExample' => CustomRunnerExample::class,
        ],
```

**Route1** config example:
```json 
{
    "name": "route_1",
    "fromSystem": {"code": "system_1"},
    "fromSystemDsn": "HTTP;POST;/v2/test-post",
    "fromSystemData": {
      "data": {
        "type": "object",
        "required": true,
        "example": "",
        "items": null,
        "validators": null,
        "properties": {
            "orderId": {
                "type": "string",
                "required": true,
                "example": "",
                "items": null,
                "properties": null,
                "validators": [{"assert" : "assertValidator", "params" : {"assertName":  "uuid"}}]
            },
            "customer": {
                "type": "object",
                "required": true,
                "example": "",
                "items": null,
                "properties": {
                    "id": {
                        "type": "int",
                        "required": true,
                        "example": "585781",
                        "items": null,
                        "properties": null,
                        "validators": [{"assert" : "assertValidator", "params" : {"assertName": "uuid","minValue" : 1}}]
                    },
                    "person": {
                        "type": "object",
                        "required": false,
                        "example": "",
                        "items": null,
                        "properties": {
                            "first_name": {
                                "type": "string",
                                "required": true,
                                "example": "Test",
                                "items": null,
                                "properties": null,
                                "validators": null
                            },
                            "last_name": {
                                "type": "string",
                                "required": true,
                                "example": "",
                                "items": null,
                                "properties": null,
                                "validators": null
                            }
                        },
                        "validators": null
                    }
                },
                "validators": null
            }
        }
      },
      "properties": {},
      "headers": {},
      "docs": {"example": ""}
    },
    "toSystem": {"code":  "system_2"},
    "toSystemDsn": "PUBSUB;route_2",
    "toSystemData": {
        "headers": {},
        "responseFormat": "json",
        "auth": {"serviceAlias":  "authServiceExample", "settings": {"some-key":  12345, "another-key":  "345"}},
        "template": "{\n    \"orderId\": \"{{ body.orderId }}\",\n    \"customer\": {\n        \"id\": {{ body.customer.id }},\n        \"name\": \"{{ body.customer.person }}\"\n    }\n}\n"
    },
    "syncTable": {"tableName" : "sync_record_example"},
    "syncSettings": {
      "pkPath": "{{ body.orderId }}",
      "responsePkPath": "{{ clientResponse.data.id }}",
      "syncOnExist": true,
      "updateRouteId": null
    },
    "postSuccessHandlers": ["postSuccessHandlerExample"],
    "postErrorHandlers": null,
    "customRunner": "customRunnerExample",
    "description": null
}
```
Example without runner, auth, syncSettings, handlers:

```json
{
  ............
    "toSystemData": {
        "headers": {},
        "responseFormat": "json",
        "auth": null,
        "template": "{\n    \"orderId\": \"{{ body.orderId }}\",\n    \"customer\": {\n        \"id\": {{ body.customer.id }},\n        \"name\": \"{{ body.customer.person }}\"\n    }\n}\n"
    },
    "syncTable": null,
    "syncSettings": null,
    "postSuccessHandlers": null,
    "postErrorHandlers": null,
  ............
}
```
Example with empty template:

```json
{
  ............,
  "toSystemData": {
    "headers": {},
    "responseFormat": "",
    "auth": null,
    "template": "\"\""
  },
  ............
}
```

### Validation

Validation is located under fromSystemData -> data, is optional, could be omitted at all **(figure1)**.
Route key `data` could be only of type: `object`, `array`.
Each row consist of 6 keys:
- **type**. Could be any of list:  `array`, `object`, `int`, `float`, `string`, `scalar`, `numeric`, `bool`. Validator will
check type match.
- **required**: `true`, `false`. Whether row is required. If `true`, also will check on empty.
- **validators**: list of validators **(figure2)**, could be `null`
- **items**: should be filled only if type is `array`, otherwise `null`. Describes each element of array **(figure3)**
- **properties**: should be filled only if type is `object`, otherwise `null`
- **example**: value example, could be ""

No validation example(figure1):
```json
{
  ............
  "fromSystemData": {
    "data": {
      "type": "object",
      "required": false,
      "validators": null,
      "items": null,
      "properties": {
        "empty_prop": {
          "type": "string",
          "required": false,
          "validators": null,
          "items": null,
          "properties": null,
          "example": ""
        }
      },
      "example": ""
    },
    "headers": [],
    "properties": [],
    "docs": {
      "example": ""
    }
  },
  ............
}
```

Validators list example(figure2):
```json
{
  ............,

  "validators": [
    {"assert": "assertValidator", "params": {"assertName": "string"}},
    {"assert": "myAwesomeValidator", "params": {}},
    {"assert": "assertValidator", "params": {"assertName": "uuid"}}
  ],
  ............
}
```

Items example.

(figure3.1):
```json
{
  ............,

  "items": {
    "type": "int",
    "required": true,
    "validators": null,
    "items": null,
    "properties": null,
    "example": ""
  },
  ............
}
```

(figure3.2):
```json
{
  ............,

  "items": {
    "type": "object",
    "required": true,
    "validators": null,
    "items": null,
    "properties": {
      "id": {
        "type": "string",
        "required": true,
        "validators": [{"assert": "assertValidator", "params": {"assertName": "uuid"}}],
        "items": null,
        "properties": null,
        "example": ""
      },
      "qty": {
        "type": "int",
        "required": true,
        "validators": [{"assert": "assertValidator", "params": {"minValue": 1, "assertName": "min"}}],
        "items": null,
        "properties": null,
        "example": ""
      }
    },
    "example": ""
  },
  ............
}
```

### Template

As a render, library use [TWIG](https://twig.symfony.com/)

JSON body example:

```php
{
    "orderId": "{{ body.orderId }}",
    "customer": {
        "id": "{{ body.customer.id }}",
        {% set companyName = body.customer.person ? body.customer.person.first_name ~ " " ~ body.customer.person.last_name : body.customer.organization.company %}
        "name": "{{ companyName }}"
    }
}
```

GET body example:

```php
'id'={{ body.id }}&'value'={{ body.value }}
```

### SyncRecord

Section describes a way, request and result will be stored, could be omitted.
Settings:
- **syncTable** - table name to store request data
- **syncSettings::pkPath** - required, unique id, to determine duplicate request
- **syncSettings::responsePkPath** - required, some important data from response 
- **syncSettings::syncOnExist**:
    - true - will send update request on duplicate call, if requestContent differ from previous call
    - false - will not send anything, in case previous request is determined by **pkPath**
- **syncSettings::updateRouteId** - another route call on update

Example with no usage of SyncRecord:

```json
    {
      ............
      "syncTable": null,
      "syncSettings": null,
      ............
    }
```

SyncRecord is set:

```json
    ............
    "syncTable": "awesome_table",
    "syncSettings": {
        "pkPath": "{{ body.number ~ '_' ~ body.increment }}",
        "responsePkPath": "{{ clientResponse.result.id }}",
        "syncOnExist": false,
        "updateRouteId": null
    },
    ............
```