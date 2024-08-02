<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\RequestHandler;

use App\Entity\Follow;
use App\Entity\User;
use App\Exception\CannotFollowMyselfException;
use App\Exception\FollowingRelationAlreadyExistsException;
use App\Exception\UserNotFoundException;
use App\Repository\FollowRepository;
use App\Repository\UserRepository;
use App\Services\RequestHandler\FollowHandler;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class FollowHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private FollowRepository $followRepository;

    private UserRepository $userRepository;

    private EntityManagerInterface $entityManager;

    private FollowHandler $followHandler;

    private User $follower;

    private User $following;

    private Follow $follow;

    public function testAddFollowFailsOnEqualIds(): void
    {
        self::expectException(CannotFollowMyselfException::class);
        $this->followHandler->addFollow(1, 1);
    }

    public function testAddFollowFailsOnFollowerThatDoesNotExists(): void
    {
        $this->userRepository->allows('find')->with(1)->andReturn(null);
        try {
            $this->followHandler->addFollow(1, 2);
        }
        catch (UserNotFoundException $e) {
            self::assertTrue($e->getIsFollower());
        }
    }

    public function testAddFollowFailsOnFollowingThatDoesNotExists(): void
    {
        $this->userRepository->allows('find')->with(1)->andReturn($this->follower);
        $this->userRepository->allows('find')->with(2)->andReturn(null);
        try {
            $this->followHandler->addFollow(1, 2);
        }
        catch (UserNotFoundException $e) {
            self::assertFalse($e->getIsFollower());
        }
    }

    public function testAddFollowFailsOnExistingFollow(): void
    {
        $this->userRepository->allows('find')->with(1)->andReturn($this->follower);
        $this->userRepository->allows('find')->with(2)->andReturn($this->following);
        $this->followRepository->allows('findOneBy')->with(['user' => 1, 'follower' => 2])->andReturn($this->follow);
        self::expectException(FollowingRelationAlreadyExistsException::class);
        $this->followHandler->addFollow(1, 2);
    }

    public function testAddFollowHappyPath(): void
    {
        $this->userRepository->allows('find')->with(1)->andReturn($this->follower);
        $this->userRepository->allows('find')->with(2)->andReturn($this->following);
        $this->followRepository->allows('findOneBy')->with(['user' => 1, 'follower' => 2])->andReturn(null);
        $this->entityManager->allows('persist')->with(Mockery::type(Follow::class));
        $this->entityManager->allows('flush')->once();
        $this->followHandler->addFollow(1, 2);
    }

    protected function setUp(): void
    {
        $this->followRepository = Mockery::mock(FollowRepository::class);
        $this->userRepository   = Mockery::mock(UserRepository::class);
        $this->entityManager    = Mockery::mock(EntityManagerInterface::class);
        $this->follower         = Mockery::mock(User::class);
        $this->following        = Mockery::mock(User::class);
        $this->follow           = Mockery::mock(Follow::class);
        $this->followHandler    = new FollowHandler($this->followRepository, $this->userRepository, $this->entityManager);
    }
}
