<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221204816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compaign ADD schedule_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE compaign ADD CONSTRAINT FK_4F2A6980A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4F2A6980A40BC2D5 ON compaign (schedule_id)');
        $this->addSql('ALTER TABLE schedule DROP timezone, CHANGE `from` `from` INT NOT NULL, CHANGE `to` `to` INT NOT NULL');
        $this->addSql('ALTER TABLE step ADD day_after_last_step INT NOT NULL, ADD start_time INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compaign DROP FOREIGN KEY FK_4F2A6980A40BC2D5');
        $this->addSql('DROP INDEX UNIQ_4F2A6980A40BC2D5 ON compaign');
        $this->addSql('ALTER TABLE compaign DROP schedule_id');
        $this->addSql('ALTER TABLE step DROP day_after_last_step, DROP start_time');
        $this->addSql('ALTER TABLE schedule ADD timezone VARCHAR(255) NOT NULL, CHANGE `from` `from` VARCHAR(255) NOT NULL, CHANGE `to` `to` VARCHAR(255) NOT NULL');
    }
}
