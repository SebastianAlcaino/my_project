<?php

namespace App\Services\Parser;

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
            "createdAt" => $tweet->getCreatedAt()->format("Y-m-d H:i:s"),
            "user" => $this->userParser->parseUser($tweet->getUser())
        ];
    }
}
