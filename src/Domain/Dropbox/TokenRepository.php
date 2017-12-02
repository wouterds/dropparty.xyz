<?php

namespace DropParty\Domain\Dropbox;

interface TokenRepository
{
    /**
     * @param Token $token
     */
    public function add(Token $token);

    /**
     * @param Token $token
     * @return bool
     * @internal param UserId $userId
     */
    public function has(Token $token): bool;

    /**
     * @param Token $token
     */
    public function update(Token $token);
}
