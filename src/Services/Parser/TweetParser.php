<?php

namespace App\Services\Parser;

use App\Entity\Like;
use App\Entity\Tweet;

class TweetParser
{
    public function __construct(private readonly UserParser $userParser)
    {
    }

    public function parseTweet(Tweet $tweet): array
    {
        return [
            "id" => $tweet->getId(),
            "tweetBody" => $tweet->getTweetBody(),
            "likes" => count($tweet->getLikes()),
            "createdAt" => $tweet->getCreatedAt()->format("Y-m-d H:i:s"),
            "user" => $this->userParser->parseUser($tweet->getUser())
        ];
    }

    public function parseTweetAllInfo(Tweet $tweet): array
    {
        $likes = [];
        foreach ($tweet->getLikes() as $like) {
            $likes[] =$this->userParser->parseUser($like->getUser()) ;
        }

        $parsedTweet=$this->parseTweet($tweet);
        //return array_push($arrayAux,$likes);
        $parsedTweet["likedBy"]=$likes;
        return $parsedTweet;


    }
}
