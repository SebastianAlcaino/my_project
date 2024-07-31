<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tweet;
use App\Entity\User;
use App\Repository\TweetRepository;
use App\Repository\UserRepository;
use App\Services\Parser\TweetParser;
use App\Services\Parser\UserParser;
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
    public function deleteTweet(TweetRepository $tweetRepository, EntityManagerinterface $entityManager, int $id): JsonResponse
    {
        $tweet = $tweetRepository->find($id);

        if ($tweet === null) {
            throw $this->createNotFoundException("No tweet found for id " . $id);
        }

        $entityManager->remove($tweet);
        $entityManager->flush();

        return new JsonResponse(["success" => true]);
    }

    #[Route(path: "/tweet/update/{id}", name: "update_tweet", methods: ["POST"])]
    public function updateTweet(TweetRepository $tweetRepository, TweetParser $tweetParser, EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $payload = $request->getPayload();
        $tweet = $tweetRepository->find($id);

        if ($tweet === null) {
            throw $this->createNotFoundException("No tweet found for id " . $id);
        }

        $tweet->setTweetBody($payload->getString("tweetBody"));
        $entityManager->flush();

        return new JsonResponse($tweetParser->parseTweet($tweet));
    }

    #[Route(path: "/tweet/show/{id}", name: "show_tweet", methods: ["GET"])]
    public function showTweet(TweetRepository $tweetRepository,TweetParser $tweetParser, int $id): JsonResponse
    {
        $tweet= $tweetRepository->find($id);

        if ($tweet === null) {
            throw $this->createNotFoundException("No tweet found for id " . $id);
        }

        return new JsonResponse($tweetParser->parseTweet($tweet));
    }

    #[Route(path: "/tweet/create/{userId}", name: "create_tweet", methods: ["POST"])]
    public function createTweet(TweetParser $tweetParser, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request, int $userId): JsonResponse
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

        return new JsonResponse($tweetParser->parseTweet($tweet));
    }

    #[Route(path: "/tweet/list-all", name: "tweet_list", methods: ["GET"])]
    public function listAllTweets(TweetRepository $tweetRepository,TweetParser $tweetParser, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var Tweet[] $tweets */
        $tweets= $tweetRepository->findAll();
        

        $arrayTweets = [];

        foreach ($tweets as $tweet) {
            $arrayTweets[] = $tweetParser->parseTweet($tweet);
        }

        return new JsonResponse($arrayTweets);
    }

    #[Route(path: "/tweet/list-all-tweets-for-a-single-user/{id}", name: "showAllTwetsByUserId", methods: ["GET"])]
    public function getTweetsByUserId(TweetParser $tweetParser, UserRepository $userRepository, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $userRepository->find($id);

        $arrayTweets = [];

        foreach ($user->getTweets() as $tweet) {

            $arrayTweets[] = $tweetParser->parseTweet($tweet);
        }


        return new JsonResponse($arrayTweets);
    }


    //mostrr el usuario con sus respectivas weas y ammountoftweets


}
