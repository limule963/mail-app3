<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223024130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4F2A69805E237E06 ON compaign (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1E87DC84E7927C74 ON dsn (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_289161CBB08E074E ON `lead` (email_address)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_43B9FE3C5E237E06 ON step (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_4F2A69805E237E06 ON compaign');
        $this->addSql('DROP INDEX UNIQ_43B9FE3C5E237E06 ON step');
        $this->addSql('DROP INDEX UNIQ_1E87DC84E7927C74 ON dsn');
        $this->addSql('DROP INDEX UNIQ_289161CBB08E074E ON `lead`');
    }
}
