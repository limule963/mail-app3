<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230224032148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE schedule ADD compaign_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FBE8F0C7C7 FOREIGN KEY (compaign_id) REFERENCES compaign (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A3811FBE8F0C7C7 ON schedule (compaign_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FBE8F0C7C7');
        $this->addSql('DROP INDEX UNIQ_5A3811FBE8F0C7C7 ON schedule');
        $this->addSql('ALTER TABLE schedule DROP compaign_id');
    }
}
