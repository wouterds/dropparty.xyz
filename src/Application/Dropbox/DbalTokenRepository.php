<?php

namespace DropParty\Application\Dropbox;

use Doctrine\DBAL\Connection;
use DropParty\Domain\Dropbox\Token;
use DropParty\Domain\Dropbox\TokenRepository;

class DbalTokenRepository implements TokenRepository
{
    public const TABLE = 'dropbox_token';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Token $token
     */
    public function add(Token $token)
    {
        $this->connection->insert(self::TABLE, [
            'user_id' => $token->getUserId(),
            'access_token' => $token->getAccessToken(),
        ]);
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function has(Token $token): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('COUNT(1)');
        $query->from(self::TABLE);
        $query->where('user_id = ' . $query->createNamedParameter($token->getUserId()));

        return (int) $query->execute()->fetchColumn(0) > 0;
    }

    /**
     * @param Token $token
     */
    public function update(Token $token)
    {
        $query = $this->connection->createQueryBuilder();
        $query->update(self::TABLE);
        $query->set('access_token', $query->createNamedParameter($token->getAccessToken()));
        $query->set('updated_at', 'NOW()');
        $query->where('user_id = ' . $query->createNamedParameter($token->getUserId()));
        $query->execute();
    }
}
