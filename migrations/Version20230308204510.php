<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308204510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_1E87DC84E7927C74 ON dsn');
        $this->addSql('DROP INDEX UNIQ_43B9FE3C5E237E06 ON step');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1E87DC84E7927C74 ON dsn (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_43B9FE3C5E237E06 ON step (name)');
    }
    
}

