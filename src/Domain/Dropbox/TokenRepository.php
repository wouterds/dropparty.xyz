<?php

namespace DropParty\Domain\Dropbox;

use DropParty\Domain\Users\UserId;

interface TokenRepository
{
    /**
     * @param Token $token
     */
    public function add(Token $token);

    /**
     * @param UserId $userId
     * @return bool
     * @internal param UserId $userId
     */
    public function hasTokenForUserId(UserId $userId): bool;

    /**
     * @param Token $token
     */
    public function update(Token $token);
}
