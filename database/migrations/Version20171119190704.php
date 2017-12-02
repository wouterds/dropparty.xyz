<?php

namespace DropParty\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171119190704 extends AbstractMigration
{
    private const TABLE = 'file_hash_id';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable(self::TABLE);
        $table->addColumn('id', 'integer')->setAutoincrement(true)->setUnsigned(true);
        $table->addColumn('file_id', 'uuid');
        $table->addColumn('created_at', 'datetime')->setDefault('CURRENT_TIMESTAMP');
        $table->setPrimaryKey(['id']);
        $table->addIndex(['file_id']);
        $table->addUniqueIndex(['id', 'file_id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable(self::TABLE);
    }
}
