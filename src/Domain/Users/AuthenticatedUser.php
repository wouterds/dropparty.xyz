<?php

namespace DropParty\Domain\Users;

class AuthenticatedUser
{
    /**
     * @var User
     */
    private $user;

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return !empty($this->user);
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
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
     * @return UserId|null
     */
    public function getUserId(): ?UserId
    {
        if (empty($this->user)) {
            return null;
        }

        return $this->user->getId();
    }
}
