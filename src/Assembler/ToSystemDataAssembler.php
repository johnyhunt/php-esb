<?php

declare(strict_types=1);

namespace ESB\Assembler;

use ESB\Entity\VO\AuthMap;
use ESB\Entity\VO\TargetRequestMap;

/** @psalm-type authMap = array{
 *     serviceAlias: string,
 *     settings: string[]
 * }
 * @psalm-type targetRequestMap = array{
 *      headers: string[],
 *      template: string|null,
 *      responseFormat: string,
 *      auth: authMap|null,
 * }
 */
class ToSystemDataAssembler
{
    /** @psalm-param targetRequestMap $row */
    public function __invoke(array $row) : TargetRequestMap
    {
        $auth = null;
        if ($row['auth'] ?? null) {
            $auth = new AuthMap(...$row['auth']);
        }
        $input = ['auth' => $auth] + $row;

        return new TargetRequestMap(...$input);
    }
}
