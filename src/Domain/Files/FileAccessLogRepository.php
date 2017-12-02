<?php

namespace DropParty\Domain\Files;

interface FileAccessLogRepository
{
    /**
     * @param FileAccessLog $fileAccessLog
     */
    public function add(FileAccessLog $fileAccessLog);
}
