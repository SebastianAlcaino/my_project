<?php

namespace App\Services\RequestHandler;

use App\Entity\Follow;
use App\Entity\User;
use App\Exception\CannotFollowMyselfException;
use App\Exception\FollowingRelationAlreadyExistsException;
use App\Exception\UserNotFoundException;
use App\Repository\FollowRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FollowHandler
{


    public function __construct(
        private readonly FollowRepository       $followRepository,
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @throws CannotFollowMyselfException
     * @throws UserNotFoundException
     * @throws FollowingRelationAlreadyExistsException
     */
    public function addFollow(int $followerId, int $followingId): void
    {
        if ($followerId == $followingId) {
            throw new CannotFollowMyselfException();
        }

        /** @var User $followerUser */
        $followerUser = $this->userRepository->find($followerId);
        if ($followerUser == null) {
            throw new UserNotFoundException(true);
        }

        /** @var User $followingUser */
        $followingUser = $this->userRepository->find($followingId);
        if ($followingUser == null) {
            throw new UserNotFoundException(false);
        }

        $existingFollow = $this->followRepository->findOneBy(['user' => $followerId, 'follower' => $followingId]);
        if ($existingFollow != null) {
            throw new FollowingRelationAlreadyExistsException();
        }

        $follow = new Follow();
        $follow->setUser($followerUser);
        $follow->setFollower($followingUser);

        $this->entityManager->persist($follow);
        $this->entityManager->flush();
    }

    public function removeFollow(int $userId, int $followerId): JsonResponse
    {

        $follow = $this->followRepository->findOneBy(['user' => $userId, 'follower' => $followerId]);

        if ($follow == null) {
            return new JsonResponse(['message' => "la relacion de seguimiento no existe para los usuarios: " . $userId . " y " . $followerId]);
        }
        $this->entityManager->remove($follow);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }


}