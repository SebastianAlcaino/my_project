<?php

namespace App\Exception;


class UserNotFoundException extends \Exception
{
    public function __construct(private readonly bool $isFollower)
    {
        parent::__construct();
    }

    public function getIsFollower(): bool
    {
        return $this->isFollower;
    }
}
