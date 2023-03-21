<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321023330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mo (id INT AUTO_INCREMENT NOT NULL, mo_lead_id INT DEFAULT NULL, step_id INT DEFAULT NULL, compaign_id INT DEFAULT NULL, date DATETIME NOT NULL, INDEX IDX_4C877BDC5576D25A (mo_lead_id), INDEX IDX_4C877BDC73B21E9C (step_id), INDEX IDX_4C877BDCE8F0C7C7 (compaign_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mr (id INT AUTO_INCREMENT NOT NULL, mr_lead_id INT DEFAULT NULL, step_id INT DEFAULT NULL, compaign_id INT DEFAULT NULL, date DATETIME NOT NULL, INDEX IDX_2F8117058F9777C (mr_lead_id), INDEX IDX_2F81170573B21E9C (step_id), INDEX IDX_2F811705E8F0C7C7 (compaign_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ms (id INT AUTO_INCREMENT NOT NULL, ms_lead_id INT DEFAULT NULL, step_id INT DEFAULT NULL, compaign_id INT DEFAULT NULL, date DATETIME NOT NULL, sender VARCHAR(255) NOT NULL, INDEX IDX_588627931F82633F (ms_lead_id), INDEX IDX_5886279373B21E9C (step_id), INDEX IDX_58862793E8F0C7C7 (compaign_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mo ADD CONSTRAINT FK_4C877BDC5576D25A FOREIGN KEY (mo_lead_id) REFERENCES `lead` (id)');
        $this->addSql('ALTER TABLE mo ADD CONSTRAINT FK_4C877BDC73B21E9C FOREIGN KEY (step_id) REFERENCES step (id)');
        $this->addSql('ALTER TABLE mo ADD CONSTRAINT FK_4C877BDCE8F0C7C7 FOREIGN KEY (compaign_id) REFERENCES compaign (id)');
        $this->addSql('ALTER TABLE mr ADD CONSTRAINT FK_2F8117058F9777C FOREIGN KEY (mr_lead_id) REFERENCES `lead` (id)');
        $this->addSql('ALTER TABLE mr ADD CONSTRAINT FK_2F81170573B21E9C FOREIGN KEY (step_id) REFERENCES step (id)');
        $this->addSql('ALTER TABLE mr ADD CONSTRAINT FK_2F811705E8F0C7C7 FOREIGN KEY (compaign_id) REFERENCES compaign (id)');
        $this->addSql('ALTER TABLE ms ADD CONSTRAINT FK_588627931F82633F FOREIGN KEY (ms_lead_id) REFERENCES `lead` (id)');
        $this->addSql('ALTER TABLE ms ADD CONSTRAINT FK_5886279373B21E9C FOREIGN KEY (step_id) REFERENCES step (id)');
        $this->addSql('ALTER TABLE ms ADD CONSTRAINT FK_58862793E8F0C7C7 FOREIGN KEY (compaign_id) REFERENCES compaign (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mo DROP FOREIGN KEY FK_4C877BDC5576D25A');
        $this->addSql('ALTER TABLE mo DROP FOREIGN KEY FK_4C877BDC73B21E9C');
        $this->addSql('ALTER TABLE mo DROP FOREIGN KEY FK_4C877BDCE8F0C7C7');
        $this->addSql('ALTER TABLE mr DROP FOREIGN KEY FK_2F8117058F9777C');
        $this->addSql('ALTER TABLE mr DROP FOREIGN KEY FK_2F81170573B21E9C');
        $this->addSql('ALTER TABLE mr DROP FOREIGN KEY FK_2F811705E8F0C7C7');
        $this->addSql('ALTER TABLE ms DROP FOREIGN KEY FK_588627931F82633F');
        $this->addSql('ALTER TABLE ms DROP FOREIGN KEY FK_5886279373B21E9C');
        $this->addSql('ALTER TABLE ms DROP FOREIGN KEY FK_58862793E8F0C7C7');
        $this->addSql('DROP TABLE mo');
        $this->addSql('DROP TABLE mr');
        $this->addSql('DROP TABLE ms');
    }
}
