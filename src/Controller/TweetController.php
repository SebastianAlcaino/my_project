<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


class TweetController extends AbstractController
{

    #[Route(path: "listar", name: "tweet_listar")]
    public function list(): JsonResponse
    {



        //  "username"=> "perrito",
        //             "tweetBody"=> "soy Un Perrito",
        //             "createdAt"=> "2024-07-11 22:08:59"},



        return $this->json([
            [
                "id" => 1,
                "username" => "perrito",
                "tweetBody" => "soy Un Perrito",
                "createdAt" => "2024-07-11 22:08:59"
            ],
            [
                "id" => 2,
                "username" => "gatito",
                "tweetBody" => "soy Un Perrito",
                "createdAt" => "2024-07-11 22:08:59"
            ],
            [
                "id" => 3,
                "username" => "pajarito",
                "tweetBody" => "soy Un Perrito",
                "createdAt" => "2024-07-11 22:08:59"
            ],
            [
                "id" => 4,
                "username" => "perrito",
                "tweetBody" => "soy Un Perrito",
                "createdAt" => "2024-07-11 22:08:59"
            ],
            [
                "id" => 5,
                "username" => "perrito",
                "tweetBody" => "soy Un Perrito",
                "createdAt" => "2024-07-11 22:08:59"
            ]




        ]);
    }
}
