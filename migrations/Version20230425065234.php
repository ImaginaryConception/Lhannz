<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425065234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proposition_jeux DROP FOREIGN KEY FK_6CF7757FA76ED395');
        $this->addSql('DROP INDEX IDX_6CF7757FA76ED395 ON proposition_jeux');
        $this->addSql('ALTER TABLE proposition_jeux ADD image LONGTEXT DEFAULT NULL, ADD type VARCHAR(255) NOT NULL, DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proposition_jeux ADD user_id INT NOT NULL, DROP image, DROP type');
        $this->addSql('ALTER TABLE proposition_jeux ADD CONSTRAINT FK_6CF7757FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6CF7757FA76ED395 ON proposition_jeux (user_id)');
    }
}
