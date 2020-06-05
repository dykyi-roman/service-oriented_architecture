<?php

declare(strict_types=1);

namespace App\Application\Auth\Request;

use App\Application\Auth\Exception\AppAuthException;
use App\Domain\Auth\Exception\AuthException;
use App\Domain\Auth\ValueObject\Email;
use App\Domain\Auth\ValueObject\FullName;
use App\Domain\Auth\ValueObject\Password;

final class SignUpRequest
{
    private Email $email;
    private Password $password;
    private FullName $fullName;

    public function __construct(string $email, string $password, string $firstName, string $lastName)
    {
        $this->assertWhenEmptyFields([$email, $password, $firstName, $lastName]);

        try {
            $this->email = new Email($email);
            $this->password = new Password($password);
            $this->fullName = new FullName($firstName, $lastName);
        } catch (AuthException $exception) {
            throw AppAuthException::domainException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @throws AppAuthException
     */
    private function assertWhenEmptyFields(array $fields): void
    {
        foreach ($fields as $field) {
            if ('' === $field) {
                throw AppAuthException::requireFieldsIsEmpty();
            }
        }
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): Password
    {
        return $this->password;
    }

    public function fullName(): FullName
    {
        return $this->fullName;
    }
}
