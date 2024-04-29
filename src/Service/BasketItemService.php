<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\BasketItemDto;
use App\Entity\BasketItem;
use App\Repository\BasketItemRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\Exception\ResourceNotFoundException;
use App\Service\Exception\SubresourceNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class BasketItemService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BasketItemRepository $repository,
        private readonly ProductRepository $productRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    /**
     * @return BasketItem[]
     */
    public function list(int $userId): array
    {
        $user = $this->userRepository->find($userId);
        if (null === $user) {
            throw new ResourceNotFoundException('User not found');
        }

        $basketItems = $this->repository->findBy(['user' => $user]);

        return $basketItems;
    }

    public function get(int $id): BasketItem
    {
        $basketItem = $this->repository->find($id);

        if (null === $basketItem) {
            throw new ResourceNotFoundException('Basket item not found');
        }

        return $basketItem;
    }

    public function insert(?int $id, BasketItemDto $dto, int $userId): BasketItem
    {
        $basketItem = new BasketItem();

        $product = $this->productRepository->find($dto->productId);
        if (null === $product) {
            throw new SubresourceNotFoundException('Product not found');
        }

        $user = $this->userRepository->find($userId);
        if (null === $user) {
            throw new SubresourceNotFoundException('User not found');
        }

        if (null !== $id) {
            $basketItem->setId($id);
        }
        $basketItem->setProduct($product);
        $basketItem->setUser($user);

        $this->entityManager->persist($basketItem);
        $this->entityManager->flush();

        return $basketItem;
    }

    public function update(int $id, BasketItemDto $dto, int $userId): BasketItem
    {
        $basketItem = $this->repository->find($id);

        if (null === $basketItem) {
            throw new ResourceNotFoundException('Basket item not found');
        }

        $product = $this->productRepository->find($dto->productId);
        if (null === $product) {
            throw new SubresourceNotFoundException('Product not found');
        }

        $user = $this->userRepository->find($userId);
        if (null === $user) {
            throw new SubresourceNotFoundException('User not found');
        }

        if (null !== $id) {
            $basketItem->setId($id);
        }
        $basketItem->setProduct($product);
        $basketItem->setUser($user);

        $this->entityManager->flush();

        return $basketItem;
    }

    public function delete(int $id): void
    {
        $basketItem = $this->repository->find($id);

        if (null === $basketItem) {
            throw new ResourceNotFoundException('Basket item not found');
        }

        $this->entityManager->remove($basketItem);
        $this->entityManager->flush();
    }
}
