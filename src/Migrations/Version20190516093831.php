<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190516093831 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invitation (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, invitation_code VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, used TINYINT(1) NOT NULL, created INT NOT NULL, UNIQUE INDEX UNIQ_F11D61A2BA14FCCC (invitation_code), INDEX IDX_F11D61A2F624B39D (sender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE associate (id INT AUTO_INCREMENT NOT NULL, associate_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', level INT NOT NULL, ancestors LONGTEXT NOT NULL, parent_id INT NOT NULL, email VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode VARCHAR(255) NOT NULL, mobile_phone VARCHAR(255) NOT NULL, home_phone VARCHAR(255) DEFAULT NULL, agreed_to_email_updates TINYINT(1) NOT NULL, agreed_to_text_message_updates TINYINT(1) NOT NULL, agreed_to_social_media_updates TINYINT(1) NOT NULL, parentId INT DEFAULT NULL, UNIQUE INDEX UNIQ_CCE5D252B0E8D99 (associate_id), INDEX IDX_CCE5D2510EE4CEE (parentId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, associate_id INT DEFAULT NULL, email VARCHAR(191) NOT NULL, password VARCHAR(255) DEFAULT NULL, roles VARCHAR(255) NOT NULL, disabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D6492B0E8D99 (associate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2F624B39D FOREIGN KEY (sender_id) REFERENCES associate (id)');
        $this->addSql('ALTER TABLE associate ADD CONSTRAINT FK_CCE5D2510EE4CEE FOREIGN KEY (parentId) REFERENCES associate (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492B0E8D99 FOREIGN KEY (associate_id) REFERENCES associate (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A2F624B39D');
        $this->addSql('ALTER TABLE associate DROP FOREIGN KEY FK_CCE5D2510EE4CEE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6492B0E8D99');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('DROP TABLE associate');
        $this->addSql('DROP TABLE user');
    }
}
