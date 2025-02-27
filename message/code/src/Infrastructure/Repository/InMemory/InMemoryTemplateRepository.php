<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\InMemory;

use App\Domain\Template\Document\Template;
use App\Domain\Template\Repository\ReadTemplateRepositoryInterface;
use App\Domain\Template\Repository\WriteTemplateRepositoryInterface;

class InMemoryTemplateRepository implements ReadTemplateRepositoryInterface, WriteTemplateRepositoryInterface
{
    public array $collection = [];

    public function findTemplate(string $name, string $type, string $lang): ?Template
    {
        foreach ($this->collection as $id => $data) {
            if ($data['name'] === $name && $data['type'] === $type) {
                return $this->collection[$id]['template'];
            }
        }

        return null;
    }

    public function edit(string $id, string $subject, string $context): bool
    {
        if (isset($this->collection[$id])) {
            /** @var Template $template */
            $template = $this->collection[$id]['template'];
            $template->setSubject($subject);
            $template->setContext($context);

            $this->collection[$id]['template'] = $template;
            return true;
        }

        return false;
    }

    public function remove(string $id): bool
    {
        if (isset($this->collection[$id])) {
            unset($this->collection[$id]);
            return true;
        }

        return false;
    }

    public function create(string $id, string $name, string $type, string $lang, string $subject, string $context): void
    {
        $template = new Template($id);
        $template->setSubject($subject);
        $template->setContext($context);
        $template->setName($name);
        $template->setType($type);
        $template->setLang($lang);

        $this->collection[$id] = [
            'name' => $name,
            'type' => $type,
            'lang' => $lang,
            'template' => $template
        ];
    }

    public function findById(string $id): ?Template
    {
        if (isset($this->collection[$id])) {
            return new Template($id);
        }

        return null;
    }

    public function all(): array
    {
        return $this->collection;
    }
}
