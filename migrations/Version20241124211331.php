<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241124211331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE helpdesk_user DROP FOREIGN KEY FK_7751CFD9700047D2');
        $this->addSql('DROP INDEX IDX_7751CFD9700047D2 ON helpdesk_user');
        $this->addSql('ALTER TABLE helpdesk_user DROP ticket_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `helpdesk_user` ADD ticket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `helpdesk_user` ADD CONSTRAINT FK_7751CFD9700047D2 FOREIGN KEY (ticket_id) REFERENCES helpdesk_ticket (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_7751CFD9700047D2 ON `helpdesk_user` (ticket_id)');
    }
}
