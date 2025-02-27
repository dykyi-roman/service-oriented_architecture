<?php

declare(strict_types=1);

namespace App\Application\Auth\Listener;

use App\Application\Auth\Events\UserRegisteredEvent;
use App\Application\Common\Service\ExceptionLogger;
use App\Domain\Message\Exception\MessageException;
use App\Domain\Message\Service\MessageAdapter;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SignUpSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private MessageAdapter $messageAdapter;

    public function __construct(MessageAdapter $messageAdapter, LoggerInterface $logger)
    {
        $this->messageAdapter = $messageAdapter;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'onUserRegistered'
        ];
    }

    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        try {
            $this->messageAdapter->sendRegistrationSuccessMessage($event->uuid(), $event->email());
        } catch (MessageException $exception) {
            $this->logger->error(...ExceptionLogger::log(__METHOD__, $exception->getMessage()));
        }
    }
}
