<?php

namespace DropParty\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171105223636 extends AbstractMigration
{
    private const TABLE = 'file_access_log';

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable(self::TABLE);
        $table->addColumn('file_id', 'uuid');
        $table->addColumn('ip', 'string')->setLength(64);
        $table->addColumn('user_agent', 'string')->setLength(128)->setNotnull(false);
        $table->addColumn('referrer', 'string')->setLength(128)->setNotnull(false);
        $table->addColumn('created_at', 'datetime')->setDefault('CURRENT_TIMESTAMP');
        $table->addIndex(['file_id']);
        $table->addIndex(['ip']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable(self::TABLE);
    }
}
