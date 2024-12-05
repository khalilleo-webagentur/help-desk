<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205201053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `helpdesk_project` (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_3CCA62609395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_temp_user` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_A4F52D1FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_ticket` (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, project_id INT NOT NULL, assignee_id INT DEFAULT NULL, label_id INT NOT NULL, type_id INT NOT NULL, status_id INT DEFAULT NULL, ticket_no INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_828A75BB9395C3F3 (customer_id), INDEX IDX_828A75BB166D1F9C (project_id), INDEX IDX_828A75BB59EC7D60 (assignee_id), INDEX IDX_828A75BB33B92F39 (label_id), INDEX IDX_828A75BBC54C8C93 (type_id), INDEX IDX_828A75BB6BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_ticket_activity` (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, user_id INT DEFAULT NULL, message LONGTEXT NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_D32B8DE1700047D2 (ticket_id), INDEX IDX_D32B8DE1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_ticket_attachment` (id INT AUTO_INCREMENT NOT NULL, ticket_id INT NOT NULL, file_no VARCHAR(100) NOT NULL, filename VARCHAR(255) NOT NULL, original_file_name VARCHAR(255) NOT NULL, size INT NOT NULL, extension VARCHAR(50) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_94BB96AE700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_ticket_comment` (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, description LONGTEXT NOT NULL, is_seen TINYINT(1) NOT NULL, seen_at DATETIME DEFAULT NULL, like_counter INT NOT NULL, dis_like_counter INT NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_A1FD5220700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_comment_user (ticket_comment_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6B73880E6EFAEF47 (ticket_comment_id), INDEX IDX_6B73880EA76ED395 (user_id), PRIMARY KEY(ticket_comment_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_ticket_label` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, color VARCHAR(50) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_ticket_status` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, color VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_ticket_type` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_user` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, token VARCHAR(100) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, is_deleted TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `helpdesk_user_setting` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_441B3C3DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `helpdesk_project` ADD CONSTRAINT FK_3CCA62609395C3F3 FOREIGN KEY (customer_id) REFERENCES `helpdesk_user` (id)');
        $this->addSql('ALTER TABLE `helpdesk_temp_user` ADD CONSTRAINT FK_A4F52D1FA76ED395 FOREIGN KEY (user_id) REFERENCES `helpdesk_user` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket` ADD CONSTRAINT FK_828A75BB9395C3F3 FOREIGN KEY (customer_id) REFERENCES `helpdesk_user` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket` ADD CONSTRAINT FK_828A75BB166D1F9C FOREIGN KEY (project_id) REFERENCES `helpdesk_project` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket` ADD CONSTRAINT FK_828A75BB59EC7D60 FOREIGN KEY (assignee_id) REFERENCES `helpdesk_user` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket` ADD CONSTRAINT FK_828A75BB33B92F39 FOREIGN KEY (label_id) REFERENCES `helpdesk_ticket_label` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket` ADD CONSTRAINT FK_828A75BBC54C8C93 FOREIGN KEY (type_id) REFERENCES `helpdesk_ticket_type` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket` ADD CONSTRAINT FK_828A75BB6BF700BD FOREIGN KEY (status_id) REFERENCES `helpdesk_ticket_status` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket_activity` ADD CONSTRAINT FK_D32B8DE1700047D2 FOREIGN KEY (ticket_id) REFERENCES `helpdesk_ticket` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket_activity` ADD CONSTRAINT FK_D32B8DE1A76ED395 FOREIGN KEY (user_id) REFERENCES `helpdesk_user` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket_attachment` ADD CONSTRAINT FK_94BB96AE700047D2 FOREIGN KEY (ticket_id) REFERENCES `helpdesk_ticket` (id)');
        $this->addSql('ALTER TABLE `helpdesk_ticket_comment` ADD CONSTRAINT FK_A1FD5220700047D2 FOREIGN KEY (ticket_id) REFERENCES `helpdesk_ticket` (id)');
        $this->addSql('ALTER TABLE ticket_comment_user ADD CONSTRAINT FK_6B73880E6EFAEF47 FOREIGN KEY (ticket_comment_id) REFERENCES `helpdesk_ticket_comment` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ticket_comment_user ADD CONSTRAINT FK_6B73880EA76ED395 FOREIGN KEY (user_id) REFERENCES `helpdesk_user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `helpdesk_user_setting` ADD CONSTRAINT FK_441B3C3DA76ED395 FOREIGN KEY (user_id) REFERENCES `helpdesk_user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `helpdesk_project` DROP FOREIGN KEY FK_3CCA62609395C3F3');
        $this->addSql('ALTER TABLE `helpdesk_temp_user` DROP FOREIGN KEY FK_A4F52D1FA76ED395');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BB9395C3F3');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BB166D1F9C');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BB59EC7D60');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BB33B92F39');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BBC54C8C93');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BB6BF700BD');
        $this->addSql('ALTER TABLE `helpdesk_ticket_activity` DROP FOREIGN KEY FK_D32B8DE1700047D2');
        $this->addSql('ALTER TABLE `helpdesk_ticket_activity` DROP FOREIGN KEY FK_D32B8DE1A76ED395');
        $this->addSql('ALTER TABLE `helpdesk_ticket_attachment` DROP FOREIGN KEY FK_94BB96AE700047D2');
        $this->addSql('ALTER TABLE `helpdesk_ticket_comment` DROP FOREIGN KEY FK_A1FD5220700047D2');
        $this->addSql('ALTER TABLE ticket_comment_user DROP FOREIGN KEY FK_6B73880E6EFAEF47');
        $this->addSql('ALTER TABLE ticket_comment_user DROP FOREIGN KEY FK_6B73880EA76ED395');
        $this->addSql('ALTER TABLE `helpdesk_user_setting` DROP FOREIGN KEY FK_441B3C3DA76ED395');
        $this->addSql('DROP TABLE `helpdesk_project`');
        $this->addSql('DROP TABLE `helpdesk_temp_user`');
        $this->addSql('DROP TABLE `helpdesk_ticket`');
        $this->addSql('DROP TABLE `helpdesk_ticket_activity`');
        $this->addSql('DROP TABLE `helpdesk_ticket_attachment`');
        $this->addSql('DROP TABLE `helpdesk_ticket_comment`');
        $this->addSql('DROP TABLE ticket_comment_user');
        $this->addSql('DROP TABLE `helpdesk_ticket_label`');
        $this->addSql('DROP TABLE `helpdesk_ticket_status`');
        $this->addSql('DROP TABLE `helpdesk_ticket_type`');
        $this->addSql('DROP TABLE `helpdesk_user`');
        $this->addSql('DROP TABLE `helpdesk_user_setting`');
    }
}
