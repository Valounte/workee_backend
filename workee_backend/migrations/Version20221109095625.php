<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221109095625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tea_or_coffee_meeting_user ADD meeting_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting_user ADD CONSTRAINT FK_CB81437067433D9C FOREIGN KEY (meeting_id) REFERENCES tea_or_coffee_meeting (id)');
        $this->addSql('CREATE INDEX IDX_CB81437067433D9C ON tea_or_coffee_meeting_user (meeting_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tea_or_coffee_meeting_user DROP FOREIGN KEY FK_CB81437067433D9C');
        $this->addSql('DROP INDEX IDX_CB81437067433D9C ON tea_or_coffee_meeting_user');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting_user DROP meeting_id');
    }
}
