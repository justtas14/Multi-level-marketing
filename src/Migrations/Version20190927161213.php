<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190927161213 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE associate ADD unique_user_name VARCHAR(255) NOT NULL');
        $this->addSql('DROP FUNCTION IF EXISTS alphanum;');
        $this->addSql("CREATE FUNCTION alphanum( str CHAR(255) charset utf8 ) RETURNS CHAR(255) charset utf8 DETERMINISTIC ".
            "BEGIN\n".
            "DECLARE i, len SMALLINT DEFAULT 1;\n".
            "DECLARE ret CHAR(255) charset utf8 DEFAULT '';\n".
            "DECLARE c CHAR(1) charset utf8;\n".
            "SET len = CHAR_LENGTH( str );\n".
            "REPEAT\n".
            "BEGIN\n".
            "SET c = MID( str, i, 1 );\n".
            "IF c REGEXP '[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ]' THEN\n".
            "SET ret=CONCAT(ret,c);\n".
            "END IF;\n".
            "SET i = i + 1;\n".
            "END;\n".
            "UNTIL i > len END REPEAT;\n".
            "RETURN ret;\n".
            "END;");
        $this->addSql('UPDATE associate SET unique_user_name=lower(CONCAT(alphanum(full_name), id))');
        $this->addSql('DROP FUNCTION IF EXISTS alphanum;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE associate DROP unique_user_name');
    }
}
