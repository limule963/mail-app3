<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230301163617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mail ADD mail_lead_id INT NOT NULL');
        $this->addSql('ALTER TABLE mail ADD CONSTRAINT FK_5126AC48DAE8C4E2 FOREIGN KEY (mail_lead_id) REFERENCES `lead` (id)');
        $this->addSql('CREATE INDEX IDX_5126AC48DAE8C4E2 ON mail (mail_lead_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mail DROP FOREIGN KEY FK_5126AC48DAE8C4E2');
        $this->addSql('DROP INDEX IDX_5126AC48DAE8C4E2 ON mail');
        $this->addSql('ALTER TABLE mail DROP mail_lead_id');
    }
}
