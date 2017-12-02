<?php

namespace DropParty\Application\Files;

use Doctrine\DBAL\Connection;
use DropParty\Domain\Files\FileAccessLog;
use DropParty\Domain\Files\FileAccessLogRepository;

class DbalFileAccessLogRepository implements FileAccessLogRepository
{
    public const TABLE = 'file_access_log';

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
     * @param FileAccessLog $fileAccessLog
     */
    public function add(FileAccessLog $fileAccessLog)
    {
        $this->connection->insert(self::TABLE, [
            'file_id' => $fileAccessLog->getFileId(),
            'ip' => $fileAccessLog->getIp(),
            'user_agent' => $fileAccessLog->getUserAgent(),
            'referrer' => $fileAccessLog->getReferrer(),
        ]);
    }
}
