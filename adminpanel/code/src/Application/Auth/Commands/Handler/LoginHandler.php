<?php

declare(strict_types=1);

namespace App\Application\Auth\Commands\Handler;

use App\Application\Auth\Commands\Command\AuthorizeCommand;
use App\Application\Auth\Commands\Command\LoginCommand;
use App\Application\Auth\Exception\AppAuthException;
use App\Domain\Auth\AuthAdapter;
use App\Domain\Auth\Exception\AuthException;
use SimpleBus\SymfonyBridge\Bus\CommandBus;

final class LoginHandler
{
    private CommandBus $commandBus;
    private AuthAdapter $authAdapter;

    public function __construct(CommandBus $commandBus, AuthAdapter $authAdapter)
    {
        $this->commandBus = $commandBus;
        $this->authAdapter = $authAdapter;
    }

    public function __invoke(LoginCommand $command): void
    {
        try {
            $loginRequest = $command->request();
            $response = $this->authAdapter->authorize($loginRequest->login(), $loginRequest->password());
            $this->commandBus->handle(new AuthorizeCommand($response->token(), $response->refreshToken()));
        } catch (AuthException $exception) {
            throw AppAuthException::domainException($exception->getMessage(), $exception->getCode());
        }
    }
}
