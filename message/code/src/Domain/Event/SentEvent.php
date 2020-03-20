<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\ValueObject\Template;
use Symfony\Contracts\EventDispatcher\Event;

class SentEvent extends Event
{
    private string $userId;

    private Template $template;

    public function __construct(string $userId, Template $template)
    {
        $this->userId = $userId;
        $this->template = $template;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getMessage(): Template
    {
        return $this->template;
    }
}
