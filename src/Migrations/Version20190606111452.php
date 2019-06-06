<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190606111452 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE associate ADD join_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE prelaunch_file CHANGE name name VARCHAR(255) NOT NULL, CHANGE context context VARCHAR(255) NOT NULL, CHANGE original_name original_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE invitation CHANGE invitation_code invitation_code VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE associate DROP join_date');
        $this->addSql('ALTER TABLE invitation CHANGE invitation_code invitation_code VARCHAR(191) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE prelaunch_file CHANGE name name VARCHAR(191) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE context context VARCHAR(191) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE original_name original_name VARCHAR(191) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
