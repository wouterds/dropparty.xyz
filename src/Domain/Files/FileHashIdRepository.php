<?php

namespace DropParty\Domain\Files;

interface FileHashIdRepository
{
    /**
     * @param FileId $id
     */
    public function add(FileId $id);

    /**
     * @param FileId $id
     * @return string|null
     */
    public function findHashIdByFileId(FileId $id): ?string;

    /**
     * @param string $id
     * @return FileId|null
     */
    public function findFileIdByHashId(string $id): ?FileId;
}
