<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241124161028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `helpdesk_project` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_3CCA6260A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_temp_user` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_A4F52D1FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_ticket` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, project_id INT NOT NULL, label_id INT NOT NULL, type_id INT NOT NULL, ticket_no INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_828A75BBA76ED395 (user_id), INDEX IDX_828A75BB166D1F9C (project_id), UNIQUE INDEX UNIQ_828A75BB33B92F39 (label_id), INDEX IDX_828A75BBC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_ticket_label` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, color VARCHAR(50) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_ticket_type` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_user` (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, name VARCHAR(150) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, token VARCHAR(100) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, is_deleted TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_7751CFD9700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_user_setting` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_441B3C3DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `helpdesk_project` ADD CONSTRAINT FK_3CCA6260A76ED395 FOREIGN KEY (user_id) REFERENCES `helpdesk_user` (id)');
        $this->addSql('ALTER TABLE `helpdesk_temp_user` ADD CONSTRAINT FK_A4F52D1FA76ED395 FOREIGN KEY (user_id) REFERENCES `helpdesk_user` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket` ADD CONSTRAINT FK_828A75BBA76ED395 FOREIGN KEY (user_id) REFERENCES `helpdesk_user` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket` ADD CONSTRAINT FK_828A75BB166D1F9C FOREIGN KEY (project_id) REFERENCES `helpdesk_project` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket` ADD CONSTRAINT FK_828A75BB33B92F39 FOREIGN KEY (label_id) REFERENCES `helpdesk_ticket_label` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket` ADD CONSTRAINT FK_828A75BBC54C8C93 FOREIGN KEY (type_id) REFERENCES `helpdesk_ticket_type` (id)');
        $this->addSql('ALTER TABLE `helpdesk_user` ADD CONSTRAINT FK_7751CFD9700047D2 FOREIGN KEY (ticket_id) REFERENCES `helpdesk_ticket` (id)');
        $this->addSql('ALTER TABLE `helpdesk_user_setting` ADD CONSTRAINT FK_441B3C3DA76ED395 FOREIGN KEY (user_id) REFERENCES `helpdesk_user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `helpdesk_project` DROP FOREIGN KEY FK_3CCA6260A76ED395');
        $this->addSql('ALTER TABLE `helpdesk_temp_user` DROP FOREIGN KEY FK_A4F52D1FA76ED395');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BBA76ED395');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BB166D1F9C');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BB33B92F39');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BBC54C8C93');
        $this->addSql('ALTER TABLE `helpdesk_user` DROP FOREIGN KEY FK_7751CFD9700047D2');
        $this->addSql('ALTER TABLE `helpdesk_user_setting` DROP FOREIGN KEY FK_441B3C3DA76ED395');
        $this->addSql('DROP TABLE `helpdesk_project`');
        $this->addSql('DROP TABLE `helpdesk_temp_user`');
        $this->addSql('DROP TABLE `helpdesk_ticket`');
        $this->addSql('DROP TABLE `helpdesk_ticket_label`');
        $this->addSql('DROP TABLE `helpdesk_ticket_type`');
        $this->addSql('DROP TABLE `helpdesk_user`');
        $this->addSql('DROP TABLE `helpdesk_user_setting`');
    }
}
