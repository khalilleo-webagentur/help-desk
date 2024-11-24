<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241124211729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE helpdesk_ticket ADD assignee_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE helpdesk_ticket ADD CONSTRAINT FK_828A75BB59EC7D60 FOREIGN KEY (assignee_id) REFERENCES `helpdesk_user` (id)');
        $this->addSql('CREATE INDEX IDX_828A75BB59EC7D60 ON helpdesk_ticket (assignee_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP FOREIGN KEY FK_828A75BB59EC7D60');
        $this->addSql('DROP INDEX IDX_828A75BB59EC7D60 ON `helpdesk_ticket`');
        $this->addSql('ALTER TABLE `helpdesk_ticket` DROP assignee_id');
    }
}
