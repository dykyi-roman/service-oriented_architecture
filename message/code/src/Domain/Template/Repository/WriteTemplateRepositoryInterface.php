<?php

declare(strict_types=1);

namespace App\Domain\Template\Repository;

interface WriteTemplateRepositoryInterface
{
    public function edit(string $id, string $subject, string $context): bool;

    public function remove(string $id): bool;

    public function create(
        string $id,
        string $name,
        string $type,
        string $lang,
        string $subject,
        string $context
    ): void;
}
