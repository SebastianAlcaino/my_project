<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\Parser\UserParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{


    #[Route(path: "/user/get-user-info/{id}", name: "user_information", methods: ["GET"])]
    public function getUserInformation(UserParser $userParser,UserRepository $userRepository, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $userRepository->find($id);
        $count = count($user->getTweets());

        return new JsonResponse(["user"=>$userParser->parseFollowing($user), "amountOfTweets"=>$count]);
    }

//    #[Route(path: "/tweet/show/{id}", name: "show_tweet",  methods: ["GET"])]
//    public function showTweet(EntityManagerInterface $entityManager, int $id): JsonResponse
//    {
//        $tweet = $entityManager->getRepository(Tweet::class)->find($id);
//
//        if ($tweet === null) {
//            throw $this->createNotFoundException("No tweet found for id " . $id);
//        }
//
//        return new JsonResponse($this->tweetStructure($tweet));
//    }

//    #[Route(path: "/tweet/list-all-tweets-for-a-single-user/{id}", name: "showAllTwetsByUserId", methods: ["GET"])]
//    public function getTweetsByUserId(UserRepository $userRepository, int $id): JsonResponse
//    {
//        /** @var User $user */
//        $user = $userRepository->find($id);
//
//        $arraytweets = [];
//
//        foreach ($user->getTweets() as $tweet) {
//
//            $arraytweets[] = $this->tweetStructure($tweet);
//        }
//
//
//        return new JsonResponse($arraytweets);
//    }


}
