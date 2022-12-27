<?php

declare(strict_types=1);

namespace ESB\Validation\Route;

use Assert\Assertion;
use Assert\AssertionFailedException;
use ESB\Exception\ValidationException;

class SyncSettingsValidator
{
    public function validate(?array $syncSettings, ?array $syncTable, string $propertyPath = 'root') : void
    {
        if ($syncSettings === null && $syncTable === null) {
            return;
        }
        try {
            Assertion::true($syncSettings !== null && $syncTable !== null, 'SyncSettingsValidator syncTable and syncSettings should be set');

            Assertion::keyExists($syncTable, 'tableName', 'SyncSettingsValidator::table::tableName expected non-blank string');
            Assertion::string($syncTable['tableName'], 'SyncSettingsValidator::table::tableName expected non-blank string');
            Assertion::notBlank($syncTable['tableName'], 'SyncSettingsValidator::table::tableName expected non-blank string');

            Assertion::keyExists($syncSettings, 'pkPath', 'SyncSettingsValidator::pkPath expected non-blank string');
            Assertion::string($syncSettings['pkPath'], 'SyncSettingsValidator::pkPath expected non-blank string');
            Assertion::notBlank($syncSettings['pkPath'], 'SyncSettingsValidator::pkPath expected non-blank string');

            Assertion::keyExists($syncSettings, 'responsePkPath', 'SyncSettingsValidator::responsePkPath expected non-blank string');
            Assertion::string($syncSettings['responsePkPath'], 'SyncSettingsValidator::responsePkPath expected non-blank string');

            Assertion::boolean($syncSettings['syncOnExist'] ?? null, 'SyncSettingsValidator::syncOnExist expected boolean');
            Assertion::boolean($syncSettings['syncOnChange'] ?? null, 'SyncSettingsValidator::syncOnChange expected boolean');

            Assertion::keyExists($syncSettings, 'updateRouteId', 'SyncSettingsValidator::updateRouteId expected null or non-blank string');
            if ($updateRouteId = $syncSettings['updateRouteId']) {
                Assertion::string($updateRouteId, 'SyncSettingsValidator::updateRouteId expected non-blank string');
                Assertion::notBlank($updateRouteId, 'SyncSettingsValidator::updateRouteId expected non-blank string');
            }
        } catch (AssertionFailedException $e) {
            throw new ValidationException($e->getMessage(), $propertyPath);
        }
    }
}
