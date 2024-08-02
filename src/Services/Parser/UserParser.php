<?php

namespace App\Services\Parser;

use App\Entity\User;

class UserParser
{
    public function parseUser(User $user): array
    {

        return ["id" => $user->getId(), "username" => $user->getUsername()];
    }


    public function parseFollowers(User $user): array
    {
        $followers = [];
        foreach ($user->getFollower() as $follower) {
            $followers[] = $this->parseUser($follower->getUser());
        }
        $parsedUser = $this->parseUser($user);
        $parsedUser["followers"] = $followers;
        return $parsedUser;
    }

    public function parseFollowing(User $user): array{
        $following = [];
        foreach ($user->getFollows() as $follow){
            $following[] = $this->parseUser($follow->getFollower());
        }
        $parsedFollowers = $this->parseFollowers($user);
        $parsedFollowers["following"] = $following;
        return $parsedFollowers;
    }




}