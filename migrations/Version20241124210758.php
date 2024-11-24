<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241124210758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE helpdesk_project DROP FOREIGN KEY FK_3CCA6260A76ED395');
        $this->addSql('DROP INDEX IDX_3CCA6260A76ED395 ON helpdesk_project');
        $this->addSql('ALTER TABLE helpdesk_project CHANGE user_id customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE helpdesk_project ADD CONSTRAINT FK_3CCA62609395C3F3 FOREIGN KEY (customer_id) REFERENCES `helpdesk_user` (id)');
        $this->addSql('CREATE INDEX IDX_3CCA62609395C3F3 ON helpdesk_project (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `helpdesk_project` DROP FOREIGN KEY FK_3CCA62609395C3F3');
        $this->addSql('DROP INDEX IDX_3CCA62609395C3F3 ON `helpdesk_project`');
        $this->addSql('ALTER TABLE `helpdesk_project` CHANGE customer_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE `helpdesk_project` ADD CONSTRAINT FK_3CCA6260A76ED395 FOREIGN KEY (user_id) REFERENCES helpdesk_user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3CCA6260A76ED395 ON `helpdesk_project` (user_id)');
    }
}
