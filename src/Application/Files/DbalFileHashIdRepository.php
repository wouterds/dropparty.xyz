<?php

namespace DropParty\Application\Files;

use Doctrine\DBAL\Connection;
use DropParty\Domain\Files\FileHashIdRepository;
use DropParty\Domain\Files\FileId;
use Hashids\Hashids;

class DbalFileHashIdRepository implements FileHashIdRepository
{
    public const TABLE = 'file_hash_id';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Hashids
     */
    private $hashids;

    /**
     * @param Connection $connection
     * @param Hashids $hashids
     */
    public function __construct(Connection $connection, Hashids $hashids)
    {
        $this->connection = $connection;
        $this->hashids = $hashids;
    }

    /**
     * @param FileId $id
     */
    public function add(FileId $id)
    {
        $this->connection->insert(self::TABLE, [
            'file_id' => $id,
        ]);
    }

    /**
     * @param FileId $id
     * @return string|null
     */
    public function findHashIdByFileId(FileId $id): ?string
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('*');
        $query->from(self::TABLE);
        $query->where('file_id = ' . $query->createNamedParameter($id));
        $result = $query->execute()->fetch();

        if (empty($result)) {
            return null;
        }

        return $this->hashids->encode($result['id']);
    }

    /**
     * @param string $id
     * @return FileId|null
     */
    public function findFileIdByHashId(string $id): ?FileId
    {
        $id = $this->hashids->decode($id);

        if (empty($id)) {
            return null;
        }

        $id = reset($id);

        $query = $this->connection->createQueryBuilder();
        $query->select('*');
        $query->from(self::TABLE);
        $query->where('id = ' . $query->createNamedParameter($id));
        $result = $query->execute()->fetch();

        if (empty($result)) {
            return null;
        }

        return new FileId($result['file_id']);
    }
}
