<?php

namespace DropParty\Domain\Users;

class AuthenticatedUser
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->user !== null;
    }
}
