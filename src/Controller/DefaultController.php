<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{
    #[Route(path: "/suma-numeros/{num1}/{num2}", name: "default_suma_2_numeros")]
    public function suma2numeros(UrlGeneratorInterface $urlGenerator, int $num1, int $num2=1): JsonResponse
    {
        $result = $num1 + $num2;





        return $this->json([
            "valorSuma" => $result,
            "urlRandomString" => $urlGenerator->generate("lucky_string_lorem", ["id" => 5]),
            "urlLuckyNumber" => $urlGenerator->generate("lucky_number", ["name"=> "miguelito"]),
        ]);
    }
}
