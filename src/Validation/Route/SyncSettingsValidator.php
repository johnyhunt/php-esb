<?php

declare(strict_types=1);

namespace ESB\Validation\Route;

use Assert\Assertion;
use Assert\AssertionFailedException;
use ESB\Exception\ValidationException;

class SyncSettingsValidator
{
    public function validate(?array $row, string $propertyPath = 'root') : void
    {
        if (! $row) {
            return;
        }
        try {
            Assertion::keyExists($row, 'table', 'SyncSettingsValidator::table required');
            if ($table = $row['table']) {
                Assertion::isArray($table, 'SyncSettingsValidator::table expected array');
                Assertion::keyExists($table, 'tableName', 'SyncSettingsValidator::table::tableName expected non-blank string');
                Assertion::string($table['tableName'], 'SyncSettingsValidator::table::tableName expected non-blank string');
                Assertion::notBlank($table['tableName'], 'SyncSettingsValidator::table::tableName expected non-blank string');
            }
            Assertion::keyExists($row, 'pkPath', 'SyncSettingsValidator::pkPath expected non-blank string');
            Assertion::string($row['pkPath'], 'SyncSettingsValidator::pkPath expected non-blank string');
            Assertion::notBlank($row['pkPath'], 'SyncSettingsValidator::pkPath expected non-blank string');

            Assertion::keyExists($row, 'responsePkPath', 'SyncSettingsValidator::responsePkPath expected non-blank string');
            Assertion::string($row['responsePkPath'], 'SyncSettingsValidator::responsePkPath expected non-blank string');
            Assertion::notBlank($row['responsePkPath'], 'SyncSettingsValidator::responsePkPath expected non-blank string');

            Assertion::boolean($row['syncOnExist'] ?? null, 'SyncSettingsValidator::syncOnExist expected boolean');
            Assertion::boolean($row['syncOnChange'] ?? null, 'SyncSettingsValidator::syncOnChange expected boolean');

            Assertion::keyExists($row, 'updateRouteId', 'SyncSettingsValidator::updateRouteId expected null or non-blank string');
            if ($updateRouteId = $row['updateRouteId']) {
                Assertion::string($updateRouteId, 'SyncSettingsValidator::updateRouteId expected non-blank string');
                Assertion::notBlank($updateRouteId, 'SyncSettingsValidator::updateRouteId expected non-blank string');
            }
        } catch (AssertionFailedException $e) {
            throw new ValidationException($e->getMessage(), $propertyPath);
        }
    }
}
