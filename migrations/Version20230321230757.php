<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321230757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `lead` ADD next_step_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `lead` ADD CONSTRAINT FK_289161CBB13C343E FOREIGN KEY (next_step_id) REFERENCES step (id)');
        $this->addSql('CREATE INDEX IDX_289161CBB13C343E ON `lead` (next_step_id)');
        $this->addSql('ALTER TABLE step ADD step_order INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `lead` DROP FOREIGN KEY FK_289161CBB13C343E');
        $this->addSql('DROP INDEX IDX_289161CBB13C343E ON `lead`');
        $this->addSql('ALTER TABLE `lead` DROP next_step_id');
        $this->addSql('ALTER TABLE step DROP step_order');
    }
}
