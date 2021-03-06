<?php

namespace DropParty\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171101202452 extends AbstractMigration
{
    private const TABLE = 'file';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable(self::TABLE);
        $table->addColumn('id', 'uuid');
        $table->addColumn('user_id', 'uuid');
        $table->addColumn('name', 'string')->setLength(64);
        $table->addColumn('content_type', 'string')->setLength(32);
        $table->addColumn('size', 'integer');
        $table->addColumn('md5', 'string')->setLength(32);
        $table->addColumn('created_at', 'datetime')->setDefault('CURRENT_TIMESTAMP');
        $table->addColumn('updated_at', 'datetime')->setNotnull(false);
        $table->addColumn('deleted_at', 'datetime')->setNotnull(false);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['user_id', 'md5']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable(self::TABLE);
    }
}
