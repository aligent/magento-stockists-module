<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Command;

interface CommandInterface
{
    /**
     * Execute import command
     *
     * @param array $bunch Array of row data
     * @return array{created: int, updated: int, deleted: int}
     */
    public function execute(array $bunch): array;
}
