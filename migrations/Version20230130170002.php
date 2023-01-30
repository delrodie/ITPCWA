<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230130170002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE en_equipe ADD titre VARCHAR(255) DEFAULT NULL, ADD page_index INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fr_equipe ADD titre VARCHAR(255) DEFAULT NULL, ADD page_index INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE en_equipe DROP titre, DROP page_index');
        $this->addSql('ALTER TABLE fr_equipe DROP titre, DROP page_index');
    }
}
