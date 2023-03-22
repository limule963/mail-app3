<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230322035633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lc (id INT AUTO_INCREMENT NOT NULL, compaign_id INT DEFAULT NULL, step_id INT DEFAULT NULL, lc_lead_id INT DEFAULT NULL, date DATETIME NOT NULL, sender VARCHAR(255) DEFAULT NULL, INDEX IDX_5C2A06B6E8F0C7C7 (compaign_id), INDEX IDX_5C2A06B673B21E9C (step_id), INDEX IDX_5C2A06B65C804A70 (lc_lead_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lc ADD CONSTRAINT FK_5C2A06B6E8F0C7C7 FOREIGN KEY (compaign_id) REFERENCES compaign (id)');
        $this->addSql('ALTER TABLE lc ADD CONSTRAINT FK_5C2A06B673B21E9C FOREIGN KEY (step_id) REFERENCES step (id)');
        $this->addSql('ALTER TABLE lc ADD CONSTRAINT FK_5C2A06B65C804A70 FOREIGN KEY (lc_lead_id) REFERENCES `lead` (id)');
        $this->addSql('ALTER TABLE compaign DROP tms, DROP tmo, DROP tmr, DROP tlc');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lc DROP FOREIGN KEY FK_5C2A06B6E8F0C7C7');
        $this->addSql('ALTER TABLE lc DROP FOREIGN KEY FK_5C2A06B673B21E9C');
        $this->addSql('ALTER TABLE lc DROP FOREIGN KEY FK_5C2A06B65C804A70');
        $this->addSql('DROP TABLE lc');
        $this->addSql('ALTER TABLE compaign ADD tms INT DEFAULT NULL, ADD tmo INT DEFAULT NULL, ADD tmr INT DEFAULT NULL, ADD tlc INT DEFAULT NULL');
    }
}
