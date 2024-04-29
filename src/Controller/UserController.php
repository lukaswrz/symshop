<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\UserDto;
use App\Service\EntityJsonSerializerService;
use App\Service\Exception\ResourceNotFoundException;
use App\Service\UserService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/users', format: 'json')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityJsonSerializerService $serializer,
        private readonly UserService $userService
    ) {
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'List of users',
    )]
    #[Route(
        '',
        name: 'app_users',
        methods: ['GET']
    )]
    public function list(): Response
    {
        return new Response(
            $this->serializer->serialize(
                $this->userService->list()
            ),
            Response::HTTP_OK
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'User identified by its ID',
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'User could not be found',
    )]
    #[Route(
        '/{user_id}',
        name: 'app_user',
        methods: ['GET']
    )]
    public function get(
        int $user_id
    ): Response {
        try {
            return new Response(
                $this->serializer->serialize(
                    $this->userService->get(
                        $user_id
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
    #[Route(
        '',
        name: 'app_user_insert',
        methods: ['POST']
    )]
    public function insert(
        #[MapRequestPayload]
        UserDto $userDto,
    ): Response {
        return new Response(
            $this->serializer->serialize(
                $this->userService->insert(
                    null,
                    $userDto
                )
            ),
            Response::HTTP_CREATED
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Existing user item has been updated',
    )]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'User has been created because it didn\'t exist before',
    )]
    #[Route(
        '/{user_id}',
        name: 'app_user_upsert',
        methods: ['PUT']
    )]
    public function upsert(
        int $user_id,
        #[MapRequestPayload]
        UserDto $userDto
    ): Response {
        try {
            return new Response(
                $this->serializer->serialize(
                    $this->userService->update(
                        $user_id,
                        $userDto
                    )
                ),
                Response::HTTP_OK
            );
        } catch (ResourceNotFoundException) {
            return new Response(
                $this->serializer->serialize(
                    $this->userService->insert(
                        $user_id,
                        $userDto
                    )
                ),
                Response::HTTP_CREATED
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
        '/{user_id}',
        name: 'app_user_delete',
        methods: ['DELETE']
    )]
    public function delete(
        int $user_id,
    ): Response {
        try {
            $this->userService->delete($user_id);
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
