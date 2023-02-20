<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220224440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE compaign (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, status_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_4F2A6980A76ED395 (user_id), INDEX IDX_4F2A69806BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, email_link VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `lead` (id INT AUTO_INCREMENT NOT NULL, compaign_id INT NOT NULL, status_id INT NOT NULL, name VARCHAR(255) NOT NULL, email_address VARCHAR(255) NOT NULL, INDEX IDX_289161CBE8F0C7C7 (compaign_id), INDEX IDX_289161CB6BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE step (id INT AUTO_INCREMENT NOT NULL, email_id INT NOT NULL, status_id INT NOT NULL, compaign_id INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_43B9FE3CA832C1C9 (email_id), INDEX IDX_43B9FE3C6BF700BD (status_id), INDEX IDX_43B9FE3CE8F0C7C7 (compaign_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE compaign ADD CONSTRAINT FK_4F2A6980A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE compaign ADD CONSTRAINT FK_4F2A69806BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE `lead` ADD CONSTRAINT FK_289161CBE8F0C7C7 FOREIGN KEY (compaign_id) REFERENCES compaign (id)');
        $this->addSql('ALTER TABLE `lead` ADD CONSTRAINT FK_289161CB6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE step ADD CONSTRAINT FK_43B9FE3CA832C1C9 FOREIGN KEY (email_id) REFERENCES email (id)');
        $this->addSql('ALTER TABLE step ADD CONSTRAINT FK_43B9FE3C6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE step ADD CONSTRAINT FK_43B9FE3CE8F0C7C7 FOREIGN KEY (compaign_id) REFERENCES compaign (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compaign DROP FOREIGN KEY FK_4F2A6980A76ED395');
        $this->addSql('ALTER TABLE compaign DROP FOREIGN KEY FK_4F2A69806BF700BD');
        $this->addSql('ALTER TABLE `lead` DROP FOREIGN KEY FK_289161CBE8F0C7C7');
        $this->addSql('ALTER TABLE `lead` DROP FOREIGN KEY FK_289161CB6BF700BD');
        $this->addSql('ALTER TABLE step DROP FOREIGN KEY FK_43B9FE3CA832C1C9');
        $this->addSql('ALTER TABLE step DROP FOREIGN KEY FK_43B9FE3C6BF700BD');
        $this->addSql('ALTER TABLE step DROP FOREIGN KEY FK_43B9FE3CE8F0C7C7');
        $this->addSql('DROP TABLE compaign');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE `lead`');
        $this->addSql('DROP TABLE step');
    }
}
