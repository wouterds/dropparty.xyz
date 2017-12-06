<?php

namespace DropParty\Application\Files;

use Doctrine\DBAL\Connection;
use DropParty\Domain\Files\File;
use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use DropParty\Domain\Users\UserId;
use Hashids\Hashids;

// TODO: Get hashids out of here

class DbalFileRepository implements FileRepository
{
    public const TABLE = 'file';

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
     * @param File $user
     */
    public function add(File $user)
    {
        $this->connection->insert(self::TABLE, [
            'id' => $user->getId(),
            'user_id' => $user->getUserId(),
            'name' => $user->getName(),
            'content_type' => $user->getContentType(),
            'size' => $user->getSize(),
            'md5' => $user->getMd5(),
            'filesystem' => $user->getFilesystem(),
        ]);
    }

    /**
     * @param FileId $id
     * @return bool
     */
    public function has(FileId $id): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('COUNT(1)');
        $query->from(self::TABLE);
        $query->where('id = ' . $query->createNamedParameter($id));

        return (int) $query->execute()->fetchColumn(0) > 0;
    }

    /**
     * @param FileId $id
     * @return File|null
     */
    public function find(FileId $id): ?File
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('f.*, fhid.id AS hash_id_raw');
        $query->from(self::TABLE, 'f');
        $query->leftJoin('f', DbalFileHashIdRepository::TABLE, 'fhid', 'f.id = fhid.file_id');
        $query->where('f.id = ' . $query->createNamedParameter($id));
        $result = $query->execute()->fetch();

        if (empty($result)) {
            return null;
        }

        if (!empty($result['hash_id_raw'])) {
            $result['hash_id'] = $this->hashids->encode($result['hash_id_raw']);
        }

        return File::fromArray($result);
    }

    /**
     * @param UserId $userId
     * @return File[]
     */
    public function findByUserId(UserId $userId): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('f.*, fhid.id AS hash_id_raw');
        $query->from(self::TABLE, 'f');
        $query->leftJoin('f', DbalFileHashIdRepository::TABLE, 'fhid', 'f.id = fhid.file_id');
        $query->where('f.user_id = ' . $query->createNamedParameter($userId));
        $query->orderBy('f.created_at', 'desc');
        $result = $query->execute()->fetchAll();

        if (empty($result)) {
            return [];
        }

        return array_map(function ($row) {
            if (!empty($row['hash_id_raw'])) {
                $row['hash_id'] = $this->hashids->encode($row['hash_id_raw']);
            }

            return File::fromArray($row);
        }, $result);
    }
}
