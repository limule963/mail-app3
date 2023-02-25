<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230225005241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dsn ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dsn ADD CONSTRAINT FK_1E87DC84A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_1E87DC84A76ED395 ON dsn (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dsn DROP FOREIGN KEY FK_1E87DC84A76ED395');
        $this->addSql('DROP INDEX IDX_1E87DC84A76ED395 ON dsn');
        $this->addSql('ALTER TABLE dsn DROP user_id');
    }
}
