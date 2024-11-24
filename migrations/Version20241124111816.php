<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241124111816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `helpdesk_ticket_type` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE helpdesk_ticket ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE helpdesk_ticket ADD CONSTRAINT FK_828A75BBC54C8C93 FOREIGN KEY (type_id) REFERENCES `helpdesk_ticket_type` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_828A75BBC54C8C93 ON helpdesk_ticket (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BBC54C8C93');
        $this->addSql('DROP TABLE `helpdesk_ticket_type`');
        $this->addSql('DROP INDEX UNIQ_828A75BBC54C8C93 ON `helpdesk_ticket`');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP type_id');
    }
}
