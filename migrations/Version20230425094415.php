<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425094415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD proposition_jeux_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C487D003D FOREIGN KEY (proposition_jeux_id) REFERENCES proposition_jeux (id)');
        $this->addSql('CREATE INDEX IDX_9474526C487D003D ON comment (proposition_jeux_id)');
        $this->addSql('ALTER TABLE proposition_jeux DROP comments');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C487D003D');
        $this->addSql('DROP INDEX IDX_9474526C487D003D ON comment');
        $this->addSql('ALTER TABLE comment DROP proposition_jeux_id');
        $this->addSql('ALTER TABLE proposition_jeux ADD comments LONGTEXT NOT NULL');
    }
}
