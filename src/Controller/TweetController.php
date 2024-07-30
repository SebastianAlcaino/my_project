<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tweet;
use App\Entity\User;
use App\Repository\TweetRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

//http://127.0.0.1:8000/
class TweetController extends AbstractController
{

    #[Route(path: "/tweet/delete/{id}", name: "delte_tweet", methods: ["POST"])]
    public function deleteTweet(EntityManagerinterface $entityManager, int $id): JsonResponse
    {

        $tweet = $entityManager->getRepository(Tweet::class)->find($id);
        if ($tweet === null) {
            throw $this->createNotFoundException("No tweet found for id " . $id);
        }

        $entityManager->remove($tweet);
        $entityManager->flush();

        return new JsonResponse(["success" => true]);
    }

    #[Route(path: "/tweet/update/{id}", name: "update_tweet", methods: ["POST"])]
    public function updateTweet(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $payload = $request->getPayload();

        $tweet = $entityManager->getRepository(Tweet::class)->find($id);
        if ($tweet === null) {
            throw $this->createNotFoundException("No tweet found for id " . $id);
        }

        $tweet->setTweetBody($payload->getString("tweetBody"));
        $entityManager->flush();

        return new JsonResponse($this->tweetStructure($tweet));
    }

    #[Route(path: "/tweet/show/{id}", name: "show_tweet",  methods: ["GET"])]
    public function showTweet(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $tweet = $entityManager->getRepository(Tweet::class)->find($id);

        if ($tweet === null) {
            throw $this->createNotFoundException("No tweet found for id " . $id);
        }

        return new JsonResponse($this->tweetStructure($tweet));
    }

    #[Route(path: "/tweet/create/{userId}", name: "create_tweet", methods: ["POST"])]
    public function createTweet(UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request, int $userId): JsonResponse
    {
        /** @var User $user */
        $user = $userRepository->find($userId);

        $payload = $request->getPayload();



        $tweet = new Tweet();
        $tweet->setTweetBody($payload->getString("tweetBody"));
        $tweet->setCreatedAt(new DateTimeImmutable());
        $tweet->setUser($user);

        $entityManager->persist($tweet);

        $entityManager->flush();

        return new JsonResponse($this->tweetStructure($tweet));
    }

    #[Route(path: "/tweet/list-all", name: "tweet_list", methods: ["GET"])]
    public function listAllTweets(UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {


        /** @var Tweet[] $tweets */
        $tweets = $entityManager->getRepository(Tweet::class)->findAll();

        $arraytweets = [];





        foreach ($tweets as $tweet) {

            $arraytweets[] = $this->tweetStructure($tweet);
        }


        return new JsonResponse($arraytweets);
    }

    #[Route(path: "/tweet/list-all-tweets-for-a-single-user/{id}", name: "showAllTwetsByUserId", methods: ["GET"])]
    public function getTweetsByUserId(UserRepository $userRepository, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $userRepository->find($id);

        $arraytweets = [];

        foreach ($user->getTweets() as $tweet) {

            $arraytweets[] = $this->tweetStructure($tweet);
        }


        return new JsonResponse($arraytweets);
    }
    private function tweetStructure(Tweet $tweet): array
    {

        return [
            "id" => $tweet->getId(),
            "tweetBody" => $tweet->getTweetBody(),
            "createdAt" => $tweet->getCreatedAt()->format("Y-m-d H:i:s"),
            "user" => [
                "id" => $tweet->getUser()->getId(),
                "username" => $tweet->getUser()->getUsername()
            ]
        ];
    }
}
