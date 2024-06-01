<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240531220754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783A9D86650F');
        $this->addSql('DROP INDEX IDX_472B783A9D86650F ON gallery');
        $this->addSql('ALTER TABLE gallery ADD user_id INT NOT NULL, DROP user_id_id');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_472B783AA76ED395 ON gallery (user_id)');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B784189D86650F');
        $this->addSql('DROP INDEX IDX_14B784189D86650F ON photo');
        $this->addSql('ALTER TABLE photo DROP user_id_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783AA76ED395');
        $this->addSql('DROP INDEX IDX_472B783AA76ED395 ON gallery');
        $this->addSql('ALTER TABLE gallery ADD user_id_id INT DEFAULT NULL, DROP user_id');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783A9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_472B783A9D86650F ON gallery (user_id_id)');
        $this->addSql('ALTER TABLE photo ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784189D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_14B784189D86650F ON photo (user_id_id)');
    }
}
