<?php

declare(strict_types=1);

namespace ESB\Validation\Route;

use Assert\Assertion;
use Assert\AssertionFailedException;
use ESB\Entity\IntegrationSystem;
use ESB\Entity\SyncTable;
use ESB\Entity\VO\SyncSettings;
use ESB\Entity\VO\TargetRequestMap;
use ESB\Exception\ESBException;
use ESB\Exception\ValidationException;
use ESB\Service\CoreRunnersPool;
use ESB\Service\PostSuccessHandlersPool;

use function implode;

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
 *      fromSystemDsn: string,
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
class RouteEntityInputValidator
{
    public function __construct(
        private readonly InputDataMapValidator $inputDataMapValidator,
        private readonly IntegrationSystemValidator $integrationSystemValidator,
        private readonly TargetRequestMapValidator $targetRequestMapValidator,
        private readonly SyncSettingsValidator $settingsValidator,
        private readonly PostSuccessHandlersPool $handlersPool,
        private readonly CoreRunnersPool $coreRunnersPool,
    ) {
    }

    /** @psalm-param inputRow $row */
    public function validate(array $row) : void
    {
        $path = 'root';
        try {
            Assertion::string($row['name'] ?? null, 'RouteEntityInputValidator::name expected been non-empty string');
            Assertion::notBlank($row['name'], 'RouteEntityInputValidator::name expected been non-empty string');

            Assertion::isArray($row['fromSystem'] ?? null, 'RouteEntityInputValidator::fromSystem expected array');
            $this->integrationSystemValidator->validate($row['fromSystem'], implode('.', [$path, 'fromSystem']));

            Assertion::string($row['fromSystemDsn'] ?? null, 'RouteEntityInputValidator::fromSystemDsn expected non-blank string');
            Assertion::notBlank($row['fromSystemDsn'] ?? null, 'RouteEntityInputValidator::fromSystemDsn expected non-blank string');

            Assertion::isArray($row['fromSystemData'] ?? null, 'RouteEntityInputValidator::fromSystemData expected array');
            $this->inputDataMapValidator->validate($row['fromSystemData'], implode('.', [$path, 'fromSystemData']));

            Assertion::isArray($row['toSystem'] ?? null, 'RouteEntityInputValidator::toSystem expected array');
            $this->integrationSystemValidator->validate($row['toSystem'], implode('.', [$path, 'toSystem']));

            Assertion::string($row['toSystemDsn'] ?? null, 'RouteEntityInputValidator::toSystemDsn expected non-blank string');
            Assertion::notBlank($row['toSystemDsn'] ?? null, 'RouteEntityInputValidator::toSystemDsn expected non-blank string');

            Assertion::isArray($row['toSystemData'] ?? null, 'RouteEntityInputValidator::toSystemData expected array');
            $this->targetRequestMapValidator->validate($row['toSystemData'], implode('.', [$path, 'toSystemData']));

            Assertion::keyExists($row, 'syncSettings', 'RouteEntityInputValidator::syncSettings expected null or array');
            $syncSettings = $row['syncSettings'];
            Assertion::nullOrIsArray($syncSettings, 'RouteEntityInputValidator::syncSettings expected null or array');
            if ($syncSettings) {
                $this->settingsValidator->validate($syncSettings, implode('.', [$path, 'syncSettings']));
            }

            Assertion::keyExists($row, 'postSuccessHandlers', 'RouteEntityInputValidator::postSuccessHandlers required');
            $postSuccessHandlers = $row['postSuccessHandlers'];
            Assertion::nullOrIsArray($postSuccessHandlers, 'RouteEntityInputValidator::postSuccessHandlers expected null or array');
            if ($postSuccessHandlers) {
                Assertion::allString($postSuccessHandlers, 'RouteEntityInputValidator::postSuccessHandlers expected string service aliases');
                foreach ($postSuccessHandlers as $handler) {
                    /** will throw ESBException if isn`t set */
                    $this->handlersPool->get($handler);
                }
            }
            Assertion::keyExists($row, 'customRunner', 'RouteEntityInputValidator::customRunner required');
            Assertion::nullOrString($row['customRunner'], 'RouteEntityInputValidator::customRunner expected null or string');
            if ($row['customRunner'] !== null) {
                Assertion::notBlank($row['customRunner'], 'RouteEntityInputValidator::customRunner expected non-blank string');

                /** will throw ESBException if isn`t set */
                $this->coreRunnersPool->get($row['customRunner']);
            }
            Assertion::keyExists($row, 'description', 'RouteEntityInputValidator::description required');
            Assertion::nullOrString($row['description'], 'RouteEntityInputValidator::description expected null or string');
            if ($row['description'] !== null) {
                Assertion::notBlank($row['description'], 'RouteEntityInputValidator::description expected non-blank string');
            }
        } catch (AssertionFailedException|ESBException $e) {
            throw new ValidationException($e->getMessage(), $path);
        }
    }
}
