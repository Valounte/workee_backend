<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221109093906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tea_or_coffee_meeting_user (id INT AUTO_INCREMENT NOT NULL, invited_user_id INT DEFAULT NULL, invitation_status TEXT NOT NULL, INDEX IDX_CB814370C58DAD6E (invited_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting_user ADD CONSTRAINT FK_CB814370C58DAD6E FOREIGN KEY (invited_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting DROP FOREIGN KEY FK_F5453875C58DAD6E');
        $this->addSql('DROP INDEX IDX_F5453875C58DAD6E ON tea_or_coffee_meeting');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting DROP invited_user_id, DROP invitation_status');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tea_or_coffee_meeting_user');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting ADD invited_user_id INT DEFAULT NULL, ADD invitation_status TEXT NOT NULL');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting ADD CONSTRAINT FK_F5453875C58DAD6E FOREIGN KEY (invited_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F5453875C58DAD6E ON tea_or_coffee_meeting (invited_user_id)');
    }
}
