<?php

declare(strict_types=1);

namespace App\Tests\Application\Handler;

use App\Application\Command\UserRegisterCommand;
use App\Application\Handler\UserRegisterHandler;
use App\Domain\Entity\User;
use App\Domain\Exception\UserException;
use App\Domain\VO\FullName;
use App\Domain\VO\UserRegistrationRequest;
use App\Infrastructure\Repository\InMemory\InMemoryUserRepository;
use Faker\Factory;
use Faker\Generator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @coversDefaultClass \App\Application\Handler\UserRegisterHandler
 */
class UserRegisterHandlerTest extends WebTestCase
{
    /**
     * @var InMemoryUserRepository|object|null
     */
    private $inMemoryUserRepository;

    private Generator $faker;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Factory::create();
    }

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        self::bootKernel();

        $container = self::$kernel->getContainer();
        $this->inMemoryUserRepository = $container->get(InMemoryUserRepository::class);
    }

    /**
     * @covers ::handle
     */
    public function testUserRegisterSuccess(): void
    {
        $dispatcherMock = $this->createMock(EventDispatcher::class);
        $dispatcherMock->expects(self::once())->method('dispatch');

        $command = $this->getUserRegisterCommand();
        (new UserRegisterHandler($this->inMemoryUserRepository, $dispatcherMock))->handle($command);

        $user = $this->inMemoryUserRepository->findUserById($command->getUuid()->toString());

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @covers ::handle
     */
    public function testUserRegisterFailed(): void
    {
        $this->expectException(UserException::class);

        $dispatcherMock = $this->createMock(EventDispatcher::class);
        $dispatcherMock->expects(self::never())->method('dispatch');

        $command = $this->getUserRegisterCommand();

        $this->createUser($command->getUuid());

        (new UserRegisterHandler($this->inMemoryUserRepository, $dispatcherMock))->handle($command);
    }

    private function getUserRegisterCommand(): UserRegisterCommand
    {
        return new UserRegisterCommand(
            Uuid::uuid4(),
            new UserRegistrationRequest(
                $this->faker->email,
                $this->faker->password,
                '+380938982443',
                new FullName($this->faker->firstName, $this->faker->lastName),
            )
        );
    }

    private function createUser(UuidInterface $uuid): void
    {
        $this->inMemoryUserRepository->createUser(
            $uuid,
            $this->faker->email,
            $this->faker->password,
            $this->faker->phoneNumber,
            $this->faker->firstName
        );
    }

    public function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }
}
