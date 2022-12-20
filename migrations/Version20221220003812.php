<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221220003812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fr_presentation (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, resume LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_725F29A6C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fr_presentation ADD CONSTRAINT FK_725F29A6C54C8C93 FOREIGN KEY (type_id) REFERENCES fr_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fr_presentation DROP FOREIGN KEY FK_725F29A6C54C8C93');
        $this->addSql('DROP TABLE fr_presentation');
    }
}