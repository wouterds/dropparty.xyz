<?php

namespace DropParty\Application\Dropbox;

use Doctrine\DBAL\Connection;
use DropParty\Domain\Dropbox\Token;
use DropParty\Domain\Dropbox\TokenRepository;
use DropParty\Domain\Users\UserId;

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
     * @param UserId $userId
     * @return Token|null
     */
    public function findActiveTokenForUserId(UserId $userId): ?Token
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('*');
        $query->from(self::TABLE);
        $query->where('user_id =' . $query->createNamedParameter($userId));
        $query->orderBy('created_at', 'DESC');
        $query->setMaxResults(1);

        $result = $query->execute()->fetch();

        if (empty($result)) {
            return null;
        }

        if (!empty($result['deleted_at'])) {
            return null;
        }

        return Token::fromArray($result);
    }

    /**
     * @param Token $token
     */
    public function delete(Token $token)
    {
        $query = $this->connection->createQueryBuilder();
        $query->update(self::TABLE);
        $query->set('updated_at', 'NOW()');
        $query->set('deleted_at', 'NOW()');
        $query->where('user_id = ' . $query->createNamedParameter($token->getUserId()));
        $query->andWhere('access_token = ' . $query->createNamedParameter($token->getAccessToken()));
        $query->execute();
    }
}
