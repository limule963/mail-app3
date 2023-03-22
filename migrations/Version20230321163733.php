<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321163733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `lead` ADD step_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `lead` ADD CONSTRAINT FK_289161CB73B21E9C FOREIGN KEY (step_id) REFERENCES step (id)');
        $this->addSql('CREATE INDEX IDX_289161CB73B21E9C ON `lead` (step_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `lead` DROP FOREIGN KEY FK_289161CB73B21E9C');
        $this->addSql('DROP INDEX IDX_289161CB73B21E9C ON `lead`');
        $this->addSql('ALTER TABLE `lead` DROP step_id');
    }
}
