<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230322175724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lc ADD dsn_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lc ADD CONSTRAINT FK_5C2A06B6DE2CEAE2 FOREIGN KEY (dsn_id) REFERENCES dsn (id)');
        $this->addSql('CREATE INDEX IDX_5C2A06B6DE2CEAE2 ON lc (dsn_id)');
        $this->addSql('ALTER TABLE mo ADD dsn_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mo ADD CONSTRAINT FK_4C877BDCDE2CEAE2 FOREIGN KEY (dsn_id) REFERENCES dsn (id)');
        $this->addSql('CREATE INDEX IDX_4C877BDCDE2CEAE2 ON mo (dsn_id)');
        $this->addSql('ALTER TABLE mr ADD dsn_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mr ADD CONSTRAINT FK_2F811705DE2CEAE2 FOREIGN KEY (dsn_id) REFERENCES dsn (id)');
        $this->addSql('CREATE INDEX IDX_2F811705DE2CEAE2 ON mr (dsn_id)');
        $this->addSql('ALTER TABLE ms ADD dsn_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ms ADD CONSTRAINT FK_58862793DE2CEAE2 FOREIGN KEY (dsn_id) REFERENCES dsn (id)');
        $this->addSql('CREATE INDEX IDX_58862793DE2CEAE2 ON ms (dsn_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lc DROP FOREIGN KEY FK_5C2A06B6DE2CEAE2');
        $this->addSql('DROP INDEX IDX_5C2A06B6DE2CEAE2 ON lc');
        $this->addSql('ALTER TABLE lc DROP dsn_id');
        $this->addSql('ALTER TABLE mr DROP FOREIGN KEY FK_2F811705DE2CEAE2');
        $this->addSql('DROP INDEX IDX_2F811705DE2CEAE2 ON mr');
        $this->addSql('ALTER TABLE mr DROP dsn_id');
        $this->addSql('ALTER TABLE mo DROP FOREIGN KEY FK_4C877BDCDE2CEAE2');
        $this->addSql('DROP INDEX IDX_4C877BDCDE2CEAE2 ON mo');
        $this->addSql('ALTER TABLE mo DROP dsn_id');
        $this->addSql('ALTER TABLE ms DROP FOREIGN KEY FK_58862793DE2CEAE2');
        $this->addSql('DROP INDEX IDX_58862793DE2CEAE2 ON ms');
        $this->addSql('ALTER TABLE ms DROP dsn_id');
    }
}
