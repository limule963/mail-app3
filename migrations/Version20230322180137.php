<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230322180137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `lead` ADD dsn_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `lead` ADD CONSTRAINT FK_289161CBDE2CEAE2 FOREIGN KEY (dsn_id) REFERENCES dsn (id)');
        $this->addSql('CREATE INDEX IDX_289161CBDE2CEAE2 ON `lead` (dsn_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `lead` DROP FOREIGN KEY FK_289161CBDE2CEAE2');
        $this->addSql('DROP INDEX IDX_289161CBDE2CEAE2 ON `lead`');
        $this->addSql('ALTER TABLE `lead` DROP dsn_id');
    }
}
