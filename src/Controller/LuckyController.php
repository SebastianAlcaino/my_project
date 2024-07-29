<?php

declare(strict_types= 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use symfony\component\Routing\Attribute\Route;


class LuckyController extends AbstractController
{
    #[Route("/lucky-number", name: "lucky_number")]
    public function number(string $name=""): Response
    {
        $number =  random_int(0, 100);

        return $this->render("lucky/number.html.twig", ["number" => $number]);
    }

    #[Route("/random-string/{id}", name: "lucky_string_lorem")]
    public function stringLorem(int $id): Response
    {
        
        if ($id > 10) {
            $stringRandom = "hola";
        }else{
            $stringRandom = "chao";
        }

        return $this->render("lucky/string_random.html.twig", ["elLorem" => $stringRandom]);
    }
}
