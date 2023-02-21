<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221142832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compaign ADD new_step_priority TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE schedule ADD `from` VARCHAR(255) NOT NULL, ADD `to` VARCHAR(255) NOT NULL, DROP fromm, DROP too');
        $this->addSql('ALTER TABLE step ADD lead_status VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compaign DROP new_step_priority');
        $this->addSql('ALTER TABLE step DROP lead_status');
        $this->addSql('ALTER TABLE schedule ADD fromm VARCHAR(255) NOT NULL, ADD too VARCHAR(255) NOT NULL, DROP `from`, DROP `to`');
    }
}
