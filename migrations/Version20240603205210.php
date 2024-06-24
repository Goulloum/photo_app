<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240603205210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gallery ADD user_id INT DEFAULT NULL, CHANGE ordering ordering INT DEFAULT 99999');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_472B783AA76ED395 ON gallery (user_id)');
        $this->addSql('ALTER TABLE photo CHANGE ordering ordering INT DEFAULT 99999');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo CHANGE ordering ordering INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783AA76ED395');
        $this->addSql('DROP INDEX IDX_472B783AA76ED395 ON gallery');
        $this->addSql('ALTER TABLE gallery DROP user_id, CHANGE ordering ordering INT DEFAULT NULL');
    }
}
