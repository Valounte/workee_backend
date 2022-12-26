<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221222110834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE personal_feedback (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, message VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_7E169A11F624B39D (sender_id), INDEX IDX_7E169A11CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE personal_feedback ADD CONSTRAINT FK_7E169A11F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE personal_feedback ADD CONSTRAINT FK_7E169A11CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personal_feedback DROP FOREIGN KEY FK_7E169A11F624B39D');
        $this->addSql('ALTER TABLE personal_feedback DROP FOREIGN KEY FK_7E169A11CD53EDB6');
        $this->addSql('DROP TABLE personal_feedback');
    }
}
