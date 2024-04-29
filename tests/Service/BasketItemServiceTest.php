<?php

declare(strict_types=1);

use App\Dto\BasketItemDto;
use App\Entity\BasketItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\BasketItemRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\BasketItemService;
use App\Service\Exception\ResourceNotFoundException;
use App\Service\Exception\SubresourceNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BasketItemServiceTest extends KernelTestCase
{
    private MockObject|EntityManagerInterface|null $entityManagerMock;

    private MockObject|BasketItemRepository|null $basketItemRepositoryMock;

    private MockObject|ProductRepository|null $productRepositoryMock;

    private MockObject|UserRepository|null $userRepositoryMock;

    public function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->basketItemRepositoryMock = $this->createMock(BasketItemRepository::class);

        $this->productRepositoryMock = $this->createMock(ProductRepository::class);

        $this->userRepositoryMock = $this->createMock(UserRepository::class);

        $this
            ->entityManagerMock
            ->method('getRepository')
            ->with(BasketItem::class)
            ->willReturn($this->basketItemRepositoryMock)
        ;

        $this
            ->entityManagerMock
            ->method('getRepository')
            ->with(Product::class)
            ->willReturn($this->productRepositoryMock)
        ;

        $this
            ->entityManagerMock
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->userRepositoryMock)
        ;
    }

    public function testListBasketItems(): void
    {
        $product = new Product();
        $product->setName('Test Product Mk2');
        $product->setPrice(100);
        $product->setStock(10);

        $user = new User();
        $user->setEmail('john.doe@yahoo.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');

        $basketItem = new BasketItem();
        $basketItem->setProduct($product);
        $basketItem->setUser($user);

        $this
            ->userRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn($user)
        ;

        $this
            ->basketItemRepositoryMock
            ->method('findBy')
            ->with(['user' => $user])
            ->willReturn([$basketItem])
        ;

        $basketItemService = new BasketItemService(
            $this->entityManagerMock,
            $this->basketItemRepositoryMock,
            $this->productRepositoryMock,
            $this->userRepositoryMock
        );

        $basketItems = $basketItemService->list(1);

        $this->assertSame(
            $product,
            $basketItems[0]->getProduct()
        );
        $this->assertSame(
            $user,
            $basketItems[0]->getUser()
        );
    }

    public function testListBasketItemsWithNonexistentUser(): void
    {
        $this
            ->userRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn(null)
        ;

        $basketItemService = new BasketItemService(
            $this->entityManagerMock,
            $this->basketItemRepositoryMock,
            $this->productRepositoryMock,
            $this->userRepositoryMock
        );

        $this->expectException(ResourceNotFoundException::class);

        $basketItemService->list(1);
    }

    public function testGetBasketItem(): void
    {
        $product = new Product();
        $product->setName('Test Product Mk2');
        $product->setPrice(100);
        $product->setStock(10);

        $user = new User();
        $user->setEmail('john.doe@yahoo.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');

        $basketItem = new BasketItem();
        $basketItem->setProduct($product);
        $basketItem->setUser($user);

        $this
            ->basketItemRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn($basketItem)
        ;

        $basketItemService = new BasketItemService(
            $this->entityManagerMock,
            $this->basketItemRepositoryMock,
            $this->productRepositoryMock,
            $this->userRepositoryMock
        );

        $basketItem = $basketItemService->get(1);

        $this->assertSame(
            $product,
            $basketItem->getProduct()
        );
        $this->assertSame(
            $user,
            $basketItem->getUser()
        );
    }

    public function testGetNonexistentBasketItem(): void
    {
        $this
            ->basketItemRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn(null)
        ;

        $basketItemService = new BasketItemService(
            $this->entityManagerMock,
            $this->basketItemRepositoryMock,
            $this->productRepositoryMock,
            $this->userRepositoryMock
        );

        $this->expectException(ResourceNotFoundException::class);

        $basketItemService->get(1);
    }

    public function testInsertBasketItem(): void
    {
        $product = new Product();
        $product->setName('Test Product Mk2');
        $product->setPrice(100);
        $product->setStock(10);

        $this
            ->productRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn($product)
        ;

        $user = new User();
        $user->setEmail('john.doe@yahoo.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');

        $this
            ->userRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn($user)
        ;

        $basketItemDto = new BasketItemDto(
            1
        );

        $basketItemService = new BasketItemService(
            $this->entityManagerMock,
            $this->basketItemRepositoryMock,
            $this->productRepositoryMock,
            $this->userRepositoryMock
        );

        $basketItem = $basketItemService->insert(null, $basketItemDto, 1);
        $this->assertSame(
            $product,
            $basketItem->getProduct()
        );
        $this->assertSame(
            $user,
            $basketItem->getUser()
        );
    }

    public function testInsertBasketItemWithNonexistentProductAndUser(): void
    {
        $this
            ->productRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn(null)
        ;

        $this
            ->userRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn(null)
        ;

        $basketItemDto = new BasketItemDto(1);

        $basketItemService = new BasketItemService(
            $this->entityManagerMock,
            $this->basketItemRepositoryMock,
            $this->productRepositoryMock,
            $this->userRepositoryMock
        );

        $this->expectException(SubresourceNotFoundException::class);

        $basketItemService->insert(null, $basketItemDto, 1);
    }

    public function testUpdateBasketItem(): void
    {
        $product2 = new Product();
        $product2->setName('Test Product Mk3');
        $product2->setPrice(200);
        $product2->setStock(5);

        $this
            ->productRepositoryMock
            ->method('find')
            ->with(2)
            ->willReturn($product2)
        ;

        $user2 = new User();
        $user2->setEmail('jane.doerino@yahoo.com');
        $user2->setFirstName('Jane');
        $user2->setLastName('Doerino');

        $this
            ->userRepositoryMock
            ->method('find')
            ->with(2)
            ->willReturn($user2)
        ;

        $basketItem = new BasketItem();
        $basketItem->setUser(new User());
        $basketItem->setProduct(new Product());

        $this
            ->basketItemRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn($basketItem)
        ;

        $basketItemService = new BasketItemService(
            $this->entityManagerMock,
            $this->basketItemRepositoryMock,
            $this->productRepositoryMock,
            $this->userRepositoryMock
        );

        $basketItem = $basketItemService->update(1, new BasketItemDto(2), 2);

        $this->assertSame(
            $product2,
            $basketItem->getProduct()
        );
        $this->assertSame(
            $user2,
            $basketItem->getUser()
        );
    }

    public function testUpdateNonexistentBasketItem(): void
    {
        $this
            ->basketItemRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn(null)
        ;

        $basketItemService = new BasketItemService(
            $this->entityManagerMock,
            $this->basketItemRepositoryMock,
            $this->productRepositoryMock,
            $this->userRepositoryMock
        );

        $this->expectException(ResourceNotFoundException::class);

        $basketItemService->update(1, new BasketItemDto(1), 1);
    }

    public function testUpdateBasketItemWithNonexistentProductAndUser(): void
    {
        $this
            ->productRepositoryMock
            ->method('find')
            ->with(2)
            ->willReturn(null)
        ;

        $this
            ->userRepositoryMock
            ->method('find')
            ->with(2)
            ->willReturn(null)
        ;

        $basketItem = new BasketItem();
        $basketItem->setUser(new User());
        $basketItem->setProduct(new Product());

        $this
            ->basketItemRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn($basketItem)
        ;

        $basketItemService = new BasketItemService(
            $this->entityManagerMock,
            $this->basketItemRepositoryMock,
            $this->productRepositoryMock,
            $this->userRepositoryMock
        );

        $this->expectException(SubresourceNotFoundException::class);

        $basketItemService->update(1, new BasketItemDto(2), 2);
    }

    public function testDeleteBasketItem(): void
    {
        $basketItem = new BasketItem();
        $basketItem->setProduct(new Product());
        $basketItem->setUser(new User());

        $this
            ->basketItemRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn($basketItem)
        ;

        $basketItemService = new BasketItemService(
            $this->entityManagerMock,
            $this->basketItemRepositoryMock,
            $this->productRepositoryMock,
            $this->userRepositoryMock
        );

        $basketItemService->delete(1);

        $this->expectNotToPerformAssertions();
    }

    public function testDeleteNonexistentBasketItem(): void
    {
        $this
            ->basketItemRepositoryMock
            ->method('find')
            ->with(1)
            ->willReturn(null)
        ;

        $basketItemService = new BasketItemService(
            $this->entityManagerMock,
            $this->basketItemRepositoryMock,
            $this->productRepositoryMock,
            $this->userRepositoryMock
        );

        $this->expectException(ResourceNotFoundException::class);

        $basketItemService->delete(1);
    }
}
