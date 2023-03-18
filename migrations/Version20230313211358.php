<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230313211358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE compaign_dsn (compaign_id INT NOT NULL, dsn_id INT NOT NULL, INDEX IDX_70BB13C3E8F0C7C7 (compaign_id), INDEX IDX_70BB13C3DE2CEAE2 (dsn_id), PRIMARY KEY(compaign_id, dsn_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE compaign_dsn ADD CONSTRAINT FK_70BB13C3E8F0C7C7 FOREIGN KEY (compaign_id) REFERENCES compaign (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE compaign_dsn ADD CONSTRAINT FK_70BB13C3DE2CEAE2 FOREIGN KEY (dsn_id) REFERENCES dsn (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dsn DROP FOREIGN KEY FK_1E87DC84E8F0C7C7');
        $this->addSql('DROP INDEX IDX_1E87DC84E8F0C7C7 ON dsn');
        $this->addSql('ALTER TABLE dsn DROP compaign_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compaign_dsn DROP FOREIGN KEY FK_70BB13C3E8F0C7C7');
        $this->addSql('ALTER TABLE compaign_dsn DROP FOREIGN KEY FK_70BB13C3DE2CEAE2');
        $this->addSql('DROP TABLE compaign_dsn');
        $this->addSql('ALTER TABLE dsn ADD compaign_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dsn ADD CONSTRAINT FK_1E87DC84E8F0C7C7 FOREIGN KEY (compaign_id) REFERENCES compaign (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1E87DC84E8F0C7C7 ON dsn (compaign_id)');
    }
}