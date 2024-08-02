<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Tweet;
use App\Entity\User;
use App\Repository\LikeRepository;
use App\Repository\TweetRepository;
use App\Repository\UserRepository;
use App\Services\Parser\TweetParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LikeController extends AbstractController
{
    #[Route('/likes/add/{tweetId}/{userId}', name: 'add_like', methods: 'POST')]
    public function add(TweetParser $tweetParser,LikeRepository $likeRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, TweetRepository $tweetRepository, int $tweetId, int $userId): JsonResponse
    {
        /** @var Tweet $tweet */
        $tweet = $tweetRepository->find($tweetId);
        if ($tweet == null) {
            return new JsonResponse(['message' => "El Tweet: " . $tweetId . " no existe"], Response::HTTP_NOT_FOUND);
        }

        /** @var User $user */
        $user = $userRepository->find($userId);
        if ($user == null) {
            return new JsonResponse(['message' => "El usuario: " . $userId . " no existe"], Response::HTTP_NOT_FOUND);
        }

        $existingLike = $likeRepository->findOneBy(['tweet' => $tweet, 'user' => $user]);

        if ($existingLike == null) {
            $like = new Like();
            $like->setTweet($tweet);
            $like->setUser($user);

            $entityManager->persist($like);
            $entityManager->flush();
            return new JsonResponse([$tweetParser->parseTweet($tweet)]);
        } else {
            return new JsonResponse(['success' => false]);
        }
    }

    #[Route('/likes/remove/{tweetId}/{userId}', name: 'remove_like', methods: 'POST')]
    public function removeLike(TweetParser $tweetParser,EntityManagerInterface $entityManager, TweetRepository $tweetRepository, UserRepository $userRepository, LikeRepository $likeRepository, int $userId, int $tweetId): JsonResponse
    {
        $tweet = $tweetRepository->find($tweetId);
        $user = $userRepository->find($userId);

        $like = $likeRepository->findOneBy(['tweet' => $tweet, 'user' => $user]);

        if ($like === null) {
            return new JsonResponse(["message"=>"objet not found for tweet: " . $tweetId . " liked by user: " . $userId]);
        }

        $entityManager->remove($like);
        $entityManager->flush();

        return new JsonResponse($tweetParser->parseTweet($tweet));
    }

}


//#[Route(path: "/tweet/delete/{id}", name: "delte_tweet", methods: ["POST"])]
//    public function deleteTweet(TweetRepository $tweetRepository, EntityManagerinterface $entityManager, int $id): JsonResponse
//{
//    $tweet = $tweetRepository->find($id);
//
//    if ($tweet === null) {
//        throw $this->createNotFoundException("No tweet found for id " . $id);
//    }
//
//    $entityManager->remove($tweet);
//    $entityManager->flush();
//
//    return new JsonResponse(["success" => true]);
//}