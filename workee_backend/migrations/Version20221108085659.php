<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221108085659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tea_or_coffee_meeting ADD meeting_type TEXT NOT NULL, DROP is_random_in_team, DROP is_team_invitation');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tea_or_coffee_meeting ADD is_random_in_team TINYINT(1) NOT NULL, ADD is_team_invitation TINYINT(1) NOT NULL, DROP meeting_type');
    }
}
