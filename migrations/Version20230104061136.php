<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230104061136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, page_index INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidat (id INT AUTO_INCREMENT NOT NULL, matricule VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenoms VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, media_lettre VARCHAR(255) DEFAULT NULL, media_cv VARCHAR(255) DEFAULT NULL, job_reference VARCHAR(255) DEFAULT NULL, recaptcha VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, localisation VARCHAR(255) DEFAULT NULL, message LONGTEXT DEFAULT NULL, statut TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE en_actualite (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, resume LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, page_index INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE en_album (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, page_index INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE en_info (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, debut DATETIME DEFAULT NULL, fin DATETIME DEFAULT NULL, traduction TINYINT(1) DEFAULT NULL, statut TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE en_job (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(255) DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, resume LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, fin DATETIME DEFAULT NULL, lieu VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, contrat VARCHAR(255) DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, page_index INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE en_presentation (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, resume LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_2F65EE9EC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE en_projet (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, resume LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, page_index INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE en_ressource (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(255) DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, extension VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE en_type (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, page_index INT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fr_actualite (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, resume LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, page_index INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fr_info (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, debut DATETIME DEFAULT NULL, fin DATETIME DEFAULT NULL, traduction TINYINT(1) DEFAULT NULL, statut TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fr_job (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(255) DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, resume LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, fin DATETIME DEFAULT NULL, lieu VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, page_index INT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, contrat VARCHAR(255) DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fr_presentation (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, resume LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_725F29A6C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fr_projet (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, resume LONGTEXT DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, tags VARCHAR(255) DEFAULT NULL, page_index INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fr_ressource (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(255) DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, extension VARCHAR(255) DEFAULT NULL, page_index INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fr_type (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, page_index INT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) DEFAULT NULL, statut TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, album_id INT DEFAULT NULL, media LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_14B784181137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slide (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, statut TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE traduction (id INT AUTO_INCREMENT NOT NULL, route VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) DEFAULT NULL, page INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL, password VARCHAR(255) NOT NULL, connexion INT DEFAULT NULL, last_connected_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visiteur (id INT AUTO_INCREMENT NOT NULL, session VARCHAR(255) DEFAULT NULL, page INT DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE en_presentation ADD CONSTRAINT FK_2F65EE9EC54C8C93 FOREIGN KEY (type_id) REFERENCES en_type (id)');
        $this->addSql('ALTER TABLE fr_presentation ADD CONSTRAINT FK_725F29A6C54C8C93 FOREIGN KEY (type_id) REFERENCES fr_type (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784181137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE en_presentation DROP FOREIGN KEY FK_2F65EE9EC54C8C93');
        $this->addSql('ALTER TABLE fr_presentation DROP FOREIGN KEY FK_725F29A6C54C8C93');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784181137ABCF');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE candidat');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE en_actualite');
        $this->addSql('DROP TABLE en_album');
        $this->addSql('DROP TABLE en_info');
        $this->addSql('DROP TABLE en_job');
        $this->addSql('DROP TABLE en_presentation');
        $this->addSql('DROP TABLE en_projet');
        $this->addSql('DROP TABLE en_ressource');
        $this->addSql('DROP TABLE en_type');
        $this->addSql('DROP TABLE fr_actualite');
        $this->addSql('DROP TABLE fr_info');
        $this->addSql('DROP TABLE fr_job');
        $this->addSql('DROP TABLE fr_presentation');
        $this->addSql('DROP TABLE fr_projet');
        $this->addSql('DROP TABLE fr_ressource');
        $this->addSql('DROP TABLE fr_type');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE slide');
        $this->addSql('DROP TABLE traduction');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE visiteur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
