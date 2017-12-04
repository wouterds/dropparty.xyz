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
     * @return Token|null
     */
    public function findActiveTokenForUserId(UserId $userId): ?Token;

    /**
     * @param Token $token
     */
    public function delete(Token $token);
}
