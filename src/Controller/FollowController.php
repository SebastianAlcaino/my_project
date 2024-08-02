<?php

namespace App\Controller;

use App\Exception\CannotFollowMyselfException;
use App\Exception\FollowingRelationAlreadyExistsException;
use App\Exception\UserNotFoundException;
use App\Services\RequestHandler\FollowHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FollowController extends AbstractController
{
    #[Route('/follow/add/{followerId}/{followingId}', name: 'add_follow', methods: 'POST')]
    public function addFollow(FollowHandler $followHandler, int $followerId, int $followingId): JsonResponse
    {
        try {
            $followHandler->addFollow($followerId, $followingId);
        } catch (CannotFollowMyselfException) {
            return new JsonResponse(['message' => "No Puedes seguirte a ti mismo"], Response::HTTP_BAD_REQUEST);
        } catch (UserNotFoundException $e) {
            if ($e->getIsFollower()) {
                return new JsonResponse(['message' => "El user: " . $followerId . " no existe"], Response::HTTP_NOT_FOUND);
            }
            return new JsonResponse(['message' => "El user a seguir: " . $followingId . " no existe"], Response::HTTP_NOT_FOUND);
        } catch (FollowingRelationAlreadyExistsException) {
            return new JsonResponse(['message' => "La relacion de seguimiento entre : " . $followerId . " y " . $followingId . " ya existe"], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(["success" => true]);
    }

    #[Route("/follow/remove/{userId}/{followerId}", name: 'remove_follow', methods: 'POST')]
    public function removeFollow(FollowHandler $followHandler, int $userId, int $followerId): JsonResponse
    {
        return $followHandler->removeFollow($userId, $followerId);
    }
}
