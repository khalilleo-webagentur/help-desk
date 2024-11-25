<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241124220450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE helpdesk_ticket ADD status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE helpdesk_ticket ADD CONSTRAINT FK_828A75BB6BF700BD FOREIGN KEY (status_id) REFERENCES `helpdesk_ticket_status` (id)');
        $this->addSql('CREATE INDEX IDX_828A75BB6BF700BD ON helpdesk_ticket (status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BB6BF700BD');
        $this->addSql('DROP INDEX IDX_828A75BB6BF700BD ON `helpdesk_ticket`');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP status_id');
    }
}
