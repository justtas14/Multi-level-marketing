<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191003130148 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE log ADD context LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD level SMALLINT NOT NULL, ADD level_name VARCHAR(50) NOT NULL, ADD extra LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP log_type, CHANGE message message LONGTEXT NOT NULL, CHANGE created created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE log ADD log_type VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP context, DROP level, DROP level_name, DROP extra, CHANGE message message VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE created_at created DATETIME DEFAULT NULL');
    }
}
