<?php

declare(strict_types=1);

namespace ESB\Validation\Route;

use Assert\Assertion;
use Assert\AssertionFailedException;
use ESB\Exception\ValidationException;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Source;

/**
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
 */
class TargetRequestMapValidator
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /** @psalm-param targetRequestMap $row */
    public function validate(array $row, string $propertyPath = 'root') : void
    {
        try {
            Assertion::keyExists($row, 'headers', 'TargetRequestMapValidator::headers should be set');
            Assertion::keyExists($row, 'template', 'TargetRequestMapValidator::template should be set');
            Assertion::keyExists($row, 'auth', 'TargetRequestMapValidator::auth should be set');
            Assertion::keyExists($row, 'responseFormat', 'TargetRequestMapValidator::responseFormat should be set');

            Assertion::isArray($row['headers'] ?? null, 'TargetRequestMapValidator::headers expected string array');
            Assertion::allString($row['headers'], 'TargetRequestMapValidator::headers expected string array');
            if ($row['template'] !== null) {
                Assertion::string($row['template'], 'TargetRequestMapValidator::template could be string only');
                Assertion::notBlank($row['template'], 'TargetRequestMapValidator::template could not be empty string');
                $this->twig->parse($this->twig->tokenize(new Source($row['template'], '')));
            }
            if ($row['auth'] !== null) {
                Assertion::isArray($row['auth'], 'TargetRequestMapValidator::auth expected array');
                $auth = $row['auth'];
                Assertion::string($auth['serviceAlias'] ?? null, 'TargetRequestMapValidator::auth::serviceAlias expected non-blank string');
                Assertion::notBlank($auth['serviceAlias'], 'TargetRequestMapValidator::auth::serviceAlias expected non-blank string');
                Assertion::isArray($auth['settings'] ?? null, 'TargetRequestMapValidator::auth::settings expected string array');
                Assertion::allString($auth['settings'], 'TargetRequestMapValidator::auth::settings expected string array');
            }
            Assertion::string($row['responseFormat'] ?? null, 'TargetRequestMapValidator::responseFormat expected string');
        } catch (AssertionFailedException|SyntaxError $e) {
            throw new ValidationException($e->getMessage(), $propertyPath);
        }
    }
}
