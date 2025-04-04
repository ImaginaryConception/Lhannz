<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230502071404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, message LONGTEXT NOT NULL, price VARCHAR(300) DEFAULT NULL, images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', status VARCHAR(15000) NOT NULL, reason LONGTEXT DEFAULT NULL, INDEX IDX_2694D7A5F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favori (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_EF85A2CCA76ED395 (user_id), INDEX IDX_EF85A2CC7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A5F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE favori ADD CONSTRAINT FK_EF85A2CCA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE favori ADD CONSTRAINT FK_EF85A2CC7294869C FOREIGN KEY (article_id) REFERENCES proposition_jeux (id)');
        $this->addSql('ALTER TABLE proposition_jeux DROP message, CHANGE title title LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A5F675F31B');
        $this->addSql('ALTER TABLE favori DROP FOREIGN KEY FK_EF85A2CCA76ED395');
        $this->addSql('ALTER TABLE favori DROP FOREIGN KEY FK_EF85A2CC7294869C');
        $this->addSql('DROP TABLE demande');
        $this->addSql('DROP TABLE favori');
        $this->addSql('ALTER TABLE proposition_jeux ADD message LONGTEXT DEFAULT NULL, CHANGE title title VARCHAR(1000) NOT NULL');
    }
}
