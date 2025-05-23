<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241219190751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE club (id_club INT AUTO_INCREMENT NOT NULL, photo VARCHAR(255) DEFAULT NULL, nom_club VARCHAR(255) NOT NULL, categorie_club VARCHAR(255) NOT NULL, date_creation_club DATE NOT NULL, adresse_club VARCHAR(255) NOT NULL, tel_club VARCHAR(15) DEFAULT NULL, site_web VARCHAR(255) NOT NULL, status_clubs VARCHAR(255) NOT NULL, attribute5 VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id_club)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(50) DEFAULT NULL, email VARCHAR(180) NOT NULL, subject VARCHAR(100) DEFAULT NULL, message TINYTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, nom_event VARCHAR(255) NOT NULL, debut_event DATE NOT NULL, fin_event DATE NOT NULL, type_event VARCHAR(255) NOT NULL, id_club INT NOT NULL, membre_inscrits JSON NOT NULL COMMENT \'(DC2Type:json)\', ressources JSON NOT NULL COMMENT \'(DC2Type:json)\', capacite INT NOT NULL, date_creation DATE NOT NULL, description_event VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE innovation (id INT AUTO_INCREMENT NOT NULL, events_id INT NOT NULL, id_membre INT NOT NULL, nom_club VARCHAR(255) NOT NULL, description_innovation VARCHAR(255) NOT NULL, titre VARCHAR(255) NOT NULL, date_creation_innovation DATE NOT NULL, status_innovation VARCHAR(255) NOT NULL, INDEX IDX_705BDF0D9D6A1065 (events_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE innovation ADD CONSTRAINT FK_705BDF0D9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE feed_backs ADD CONSTRAINT FK_5651F77933CE2470 FOREIGN KEY (id_club) REFERENCES club (id_club)');
        $this->addSql('ALTER TABLE feed_backs ADD CONSTRAINT FK_5651F7796B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955FC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressources (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feed_backs DROP FOREIGN KEY FK_5651F77933CE2470');
        $this->addSql('ALTER TABLE innovation DROP FOREIGN KEY FK_705BDF0D9D6A1065');
        $this->addSql('DROP TABLE club');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE innovation');
        $this->addSql('ALTER TABLE feed_backs DROP FOREIGN KEY FK_5651F7796B3CA4B');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955FC6CD52A');
    }
}
