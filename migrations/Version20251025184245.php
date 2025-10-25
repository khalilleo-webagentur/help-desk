<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251025184245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `helpdesk_time_track` (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, user_id INT DEFAULT NULL, minutes INT NOT NULL, note VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_87102AE1700047D2 (ticket_id), INDEX IDX_87102AE1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `helpdesk_time_track` ADD CONSTRAINT FK_87102AE1700047D2 FOREIGN KEY (ticket_id) REFERENCES `helpdesk_ticket` (id)');
        $this->addSql('ALTER TABLE `helpdesk_time_track` ADD CONSTRAINT FK_87102AE1A76ED395 FOREIGN KEY (user_id) REFERENCES `helpdesk_user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `helpdesk_time_track` DROP FOREIGN KEY FK_87102AE1700047D2');
        $this->addSql('ALTER TABLE `helpdesk_time_track` DROP FOREIGN KEY FK_87102AE1A76ED395');
        $this->addSql('DROP TABLE `helpdesk_time_track`');
    }
}
