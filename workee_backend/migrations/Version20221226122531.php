<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221226122531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE professional_development_goal (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, goal VARCHAR(255) NOT NULL, goal_status TEXT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_6CF7CF7FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professional_development_sub_goal (id INT AUTO_INCREMENT NOT NULL, goal_id INT DEFAULT NULL, sub_goal VARCHAR(255) NOT NULL, sub_goal_status TEXT NOT NULL, INDEX IDX_11C09C39667D1AFE (goal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE professional_development_goal ADD CONSTRAINT FK_6CF7CF7FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE professional_development_sub_goal ADD CONSTRAINT FK_11C09C39667D1AFE FOREIGN KEY (goal_id) REFERENCES professional_development_goal (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE professional_development_goal DROP FOREIGN KEY FK_6CF7CF7FA76ED395');
        $this->addSql('ALTER TABLE professional_development_sub_goal DROP FOREIGN KEY FK_11C09C39667D1AFE');
        $this->addSql('DROP TABLE professional_development_goal');
        $this->addSql('DROP TABLE professional_development_sub_goal');
    }
}
