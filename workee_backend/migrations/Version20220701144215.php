<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220701144215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE humidity_metric (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_F8AA7F6EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE temperature_metric (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_8EAA1620A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE humidity_metric ADD CONSTRAINT FK_F8AA7F6EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE temperature_metric ADD CONSTRAINT FK_8EAA1620A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE humidity_metric');
        $this->addSql('DROP TABLE temperature_metric');
    }
}
