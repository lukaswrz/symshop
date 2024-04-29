<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\BasketItemDto;
use App\Service\BasketItemService;
use App\Service\EntityJsonSerializerService;
use App\Service\Exception\ResourceNotFoundException;
use App\Service\Exception\SubresourceNotFoundException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/users/{user_id}/basket-items', format: 'json')]
class BasketItemController extends AbstractController
{
    public function __construct(
        private EntityJsonSerializerService $serializer,
        private BasketItemService $basketItemService
    ) {
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'List of basket items associated with the user',
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'User could not be found',
    )]
    #[Route(
        '',
        name: 'app_basket_items',
        methods: ['GET']
    )]
    public function list(
        int $user_id
    ): Response {
        try {
            return new Response(
                $this->serializer->serialize(
                    $this->basketItemService->list($user_id)
                ),
                Response::HTTP_OK
            );
        } catch (ResourceNotFoundException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        }
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Basket item identified by its ID',
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Basket item could not be found',
    )]
    #[Route(
        '/{basket_item_id}',
        name: 'app_basket_item',
        methods: ['GET']
    )]
    public function get(
        int $basket_item_id
    ): Response {
        try {
            return new Response(
                $this->serializer->serialize(
                    $this->basketItemService->get(
                        $basket_item_id
                    )
                ),
                Response::HTTP_OK
            );
        } catch (ResourceNotFoundException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        }
    }

    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Basket item has been created',
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Specified basket item is malformed (e.g., the referenced product doesn\'t exist)',
    )]
    #[Route(
        '',
        name: 'app_basket_item_insert',
        methods: ['POST']
    )]
    public function insert(
        int $user_id,
        #[MapRequestPayload]
        BasketItemDto $basketItemDto,
    ): Response {
        try {
            return new Response(
                $this->serializer->serialize(
                    $this->basketItemService->insert(
                        null,
                        $basketItemDto,
                        $user_id
                    )
                ),
                Response::HTTP_CREATED
            );
        } catch (SubresourceNotFoundException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Existing basket item has been updated',
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Basket item has been created because it didn\'t exist before',
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Specified basket item is malformed (e.g., the referenced product doesn\'t exist)',
    )]
    #[Route(
        '/{basket_item_id}',
        name: 'app_basket_item_upsert',
        methods: ['PUT']
    )]
    public function upsert(
        int $user_id,
        int $basket_item_id,
        #[MapRequestPayload]
        BasketItemDto $basketItemDto
    ): Response {
        try {
            try {
                return new Response(
                    $this->serializer->serialize(
                        $this->basketItemService->update(
                            $basket_item_id,
                            $basketItemDto,
                            $user_id
                        )
                    ),
                    Response::HTTP_OK
                );
            } catch (ResourceNotFoundException) {
                return new Response(
                    $this->serializer->serialize(
                        $this->basketItemService->insert(
                            $basket_item_id,
                            $basketItemDto,
                            $user_id
                        )
                    ),
                    Response::HTTP_CREATED
                );
            }
        } catch (SubresourceNotFoundException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Basket item has been deleted',
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Basket item could not be found',
    )]
    #[Route(
        '/{basket_item_id}',
        name: 'app_basket_item_delete',
        methods: ['DELETE']
    )]
    public function delete(
        int $basket_item_id,
    ): Response {
        try {
            $this->basketItemService->delete($basket_item_id);
        } catch (ResourceNotFoundException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        }

        return new Response(
            '',
            Response::HTTP_NO_CONTENT
        );
    }
}
