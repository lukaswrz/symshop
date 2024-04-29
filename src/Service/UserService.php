<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Exception\ResourceNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $repository
    ) {
    }

    /**
     * @return User[]
     */
    public function list(): array
    {
        $users = $this->repository->findAll();

        return $users;
    }

    public function get(int $id): ?User
    {
        $user = $this->repository->find($id);

        if (null === $user) {
            throw new ResourceNotFoundException('User not found');
        }

        return $user;
    }

    public function insert(?int $id, UserDto $dto): User
    {
        $user = new User();

        if (null !== $id) {
            $user->setId($id);
        }
        $user->setEmail($dto->email);
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function update(int $id, UserDto $dto): User
    {
        $user = $this->repository->find($id);

        if (null === $user) {
            throw new ResourceNotFoundException('User not found');
        }

        $user->setEmail($dto->email);
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);

        $this->entityManager->flush();

        return $user;
    }

    public function delete(int $id): void
    {
        $user = $this->repository->find($id);

        if (null === $user) {
            throw new ResourceNotFoundException('User not found');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
