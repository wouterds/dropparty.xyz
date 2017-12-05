<?php declare(strict_types = 1);

namespace DropParty\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171205011640 extends AbstractMigration
{
    private const TABLE = 'file';

    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE '.self::TABLE.' ADD COLUMN `filesystem` ENUM(\'LOCAL\', \'DROPBOX\') NOT NULL DEFAULT \'LOCAL\' AFTER `md5`');
        $this->addSql('CREATE INDEX idx_filesystem ON '.self::TABLE.' (`filesystem`)');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP INDEX idx_filesystem ON '.self::TABLE);
        $table = $schema->getTable(self::TABLE);
        $table->dropColumn('filesystem');
    }
}
