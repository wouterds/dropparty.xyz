<?php

namespace DropParty\Domain\Files;

use DropParty\Domain\Users\UserId;

interface FileRepository
{
    /**
     * @param File $file
     */
    public function add(File $file);

    /**
     * @param FileId $id
     * @return bool
     */
    public function has(FileId $id): bool;

    /**
     * @param FileId $id
     * @return File|null
     */
    public function find(FileId $id): ?File;

    /**
     * @param UserId $id
     * @return array
     */
    public function findByUserId(UserId $id): array;
}
