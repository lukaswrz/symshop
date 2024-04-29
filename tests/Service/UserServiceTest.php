<?php

declare(strict_types=1);

use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Exception\ResourceNotFoundException;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserServiceTest extends KernelTestCase
{
    private MockObject|EntityManagerInterface $entityManagerMock;

    private MockObject|UserRepository $userRepositoryMock;

    public function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->userRepositoryMock = $this->createMock(UserRepository::class);

        $this
            ->entityManagerMock
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->userRepositoryMock)
        ;
    }

    public function testListUsers(): void
    {
        $user = new User();
        $user->setEmail('john.doe@yahoo.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');

        $this
            ->userRepositoryMock
            ->method('findAll')
            ->willReturn([$user])
        ;

        $userService = new UserService(
            $this->entityManagerMock,
            $this->userRepositoryMock
        );

        $users = $userService->list();

        $this->assertSame(
            'john.doe@yahoo.com',
            $users[0]->getEmail()
        );
        $this->assertSame(
            'John',
            $users[0]->getFirstName()
        );
        $this->assertSame(
            'Doe',
            $users[0]->getLastName()
        );
    }

    public function testGetUser(): void
    {
        $testUser = new User();
        $testUser->setEmail('john.doe@yahoo.com');
        $testUser->setFirstName('John');
        $testUser->setLastName('Doe');

        $this
            ->userRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn($testUser)
        ;

        $userService = new UserService(
            $this->entityManagerMock,
            $this->userRepositoryMock
        );

        $user = $userService->get(1);

        $this->assertSame(
            'john.doe@yahoo.com',
            $user->getEmail()
        );
        $this->assertSame(
            'John',
            $user->getFirstName()
        );
        $this->assertSame(
            'Doe',
            $user->getLastName()
        );
    }

    public function testGetNonexistentUser(): void
    {
        $this
            ->userRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn(null)
        ;

        $userService = new UserService(
            $this->entityManagerMock,
            $this->userRepositoryMock
        );

        $this->expectException(ResourceNotFoundException::class);

        $userService->get(1);
    }

    public function testInsertUser(): void
    {
        $userService = new UserService(
            $this->entityManagerMock,
            $this->userRepositoryMock
        );

        $userDto = new UserDto(
            'john.doe@yahoo.com',
            'John',
            'Doe'
        );

        $testUser = $userService->insert(null, $userDto);

        $this->assertSame(
            'john.doe@yahoo.com',
            $testUser->getEmail()
        );
        $this->assertSame(
            'John',
            $testUser->getFirstName()
        );
        $this->assertSame(
            'Doe',
            $testUser->getLastName()
        );
    }

    public function testUpdateUser(): void
    {
        $testUser = new User();
        $testUser->setEmail('john.doe@yahoo.com');
        $testUser->setFirstName('John');
        $testUser->setLastName('Doe');

        $this
            ->userRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn($testUser);

        $userService = new UserService(
            $this->entityManagerMock,
            $this->userRepositoryMock
        );

        $testUser = $userService->update(1, new UserDto(
            'jane.doe@yahoo.com',
            'Jane',
            'Doerino'
        ));

        $this->assertSame(
            'jane.doe@yahoo.com',
            $testUser->getEmail()
        );
        $this->assertSame(
            'Jane',
            $testUser->getFirstName()
        );
        $this->assertSame(
            'Doerino',
            $testUser->getLastName()
        );
    }

    public function testUpdateNonexistentUser(): void
    {
        $this
            ->userRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $userService = new UserService(
            $this->entityManagerMock,
            $this->userRepositoryMock
        );

        $this->expectException(ResourceNotFoundException::class);

        $userService->update(1, new UserDto(
            'jane.doe@yahoo.com',
            'Jane',
            'Doerino'
        ));
    }

    public function testDeleteUser(): void
    {
        $testUser = new User();
        $testUser->setEmail('john.doe@yahoo.com');
        $testUser->setFirstName('John');
        $testUser->setLastName('Doe');

        $this
            ->userRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn($testUser);

        $userService = new UserService(
            $this->entityManagerMock,
            $this->userRepositoryMock
        );

        $userService->delete(1);

        $this->expectNotToPerformAssertions();
    }

    public function testDeleteNonexistentUser(): void
    {
        $this
            ->userRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $userService = new UserService(
            $this->entityManagerMock,
            $this->userRepositoryMock
        );

        $this->expectException(ResourceNotFoundException::class);

        $userService->delete(1);
    }
}
