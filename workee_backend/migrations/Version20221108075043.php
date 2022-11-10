<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221108075043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tea_or_coffee_meeting (id INT AUTO_INCREMENT NOT NULL, initiator_id INT DEFAULT NULL, invited_user_id INT DEFAULT NULL, is_random_in_team TINYINT(1) NOT NULL, is_team_invitation TINYINT(1) NOT NULL, invitation_status TEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_F54538757DB3B714 (initiator_id), INDEX IDX_F5453875C58DAD6E (invited_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting ADD CONSTRAINT FK_F54538757DB3B714 FOREIGN KEY (initiator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting ADD CONSTRAINT FK_F5453875C58DAD6E FOREIGN KEY (invited_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tea_or_coffee_meeting');
    }
}
