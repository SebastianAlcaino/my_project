<?php

namespace App\Services\Parser;

use App\Entity\User;

class UserParser
{
    public function parseUser(User $user): array
    {

        return ["id" => $user->getId(), "username" => $user->getUsername()];
    }
}