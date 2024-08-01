<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Entity\User;
use App\Repository\FollowRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FollowController extends AbstractController
{


    #[Route('/follow/add/{userId}/{followerId}', name: 'add_follow', methods: 'POST')]
    public function addFollow(FollowRepository $followRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, int $userId, int $followerId): JsonResponse
    {
        if ($userId == $followerId) {
            return new JsonResponse(['message' => "No Puedes seguirte a ti mismo"], Response::HTTP_BAD_REQUEST);
        }

        /** @var User $user */
        $user = $userRepository->find($userId);
        if ($user == null) {
            return new JsonResponse(['message' => "El user: " . $userId . " no existe"], Response::HTTP_NOT_FOUND);
        }

        /** @var User $follower */
        $follower = $userRepository->find($followerId);
        if ($follower == null) {
            return new JsonResponse(['message' => "El user a seguir: " . $followerId . " no existe"], Response::HTTP_NOT_FOUND);
        }

        $existingFollow = $followRepository->findOneBy(['user' => $userId, 'follower' => $followerId]);
        if ($existingFollow != null) {
            return new JsonResponse(['success' => false]);
        }

        $follow = new Follow();
        $follow->setUser($user);
        $follow->setFollower($follower);

        $entityManager->persist($follow);
        $entityManager->flush();

        return new JsonResponse(["success" => true], Response::HTTP_OK);


    }

    #[Route("/follow/remove/{userId}/{followerId}", name: 'remove_follow', methods: 'POST')]
    public function removeFollow(FollowRepository $followRepository, EntityManagerInterface $entityManager, int $userId, int $followerId): JsonResponse
    {
        $follow = $followRepository->findOneBy(['user' => $userId, 'follower' => $followerId]);

        if ($follow == null) {
            return new JsonResponse(['message' => "la relacion de seguimiento no existe para los usuarios: " . $userId . " y " . $followerId]);
        }
        $entityManager->remove($follow);
        $entityManager->flush();

        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }


}


