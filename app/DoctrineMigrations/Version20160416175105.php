<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160416175105 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE language ADD COLUMN is_default BOOLEAN DEFAULT \'0\'');

        $this->addSql("UPDATE language SET is_default='0'");
        $this->addSql("UPDATE language SET is_default='1' WHERE id=1");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_D4DB71B577153098');
        $this->addSql('CREATE TEMPORARY TABLE __temp__language AS SELECT id, code, name FROM language');
        $this->addSql('DROP TABLE language');
        $this->addSql('CREATE TABLE language (id INTEGER NOT NULL, code CHAR(2) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO language (id, code, name) SELECT id, code, name FROM __temp__language');
        $this->addSql('DROP TABLE __temp__language');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4DB71B577153098 ON language (code)');
    }
}
