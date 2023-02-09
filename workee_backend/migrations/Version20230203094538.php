<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230203094538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE daily_feedback (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, team_id INT NOT NULL, satisfaction_degree INT NOT NULL, message VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_6826938CA76ED395 (user_id), INDEX IDX_6826938C296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE daily_feedback_team_preferences (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, sending_time VARCHAR(255) NOT NULL, INDEX IDX_2FD835E296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE environment_metrics_preferences (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, metric_type VARCHAR(255) NOT NULL, is_desactivated TINYINT(1) NOT NULL, INDEX IDX_9053EF97A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE humidity_metric (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_F8AA7F6EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_FBD8E0F8979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_permission (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_8A3E02B7BE04EA9 (job_id), INDEX IDX_8A3E02B7FED90CCA (permission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, code INT NOT NULL, context TEXT NOT NULL, exception_string VARCHAR(255) DEFAULT NULL, alert TEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_F08FC65CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE luminosity_metric (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_FAED7C8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, receiver_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, alert_level TEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_BF5476CAF624B39D (sender_id), INDEX IDX_BF5476CACD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_preferences (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, alert_level TEXT NOT NULL, is_mute TINYINT(1) NOT NULL, INDEX IDX_3CAA95B4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, name TEXT NOT NULL, context TEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personal_feedback (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, message VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_7E169A11F624B39D (sender_id), INDEX IDX_7E169A11CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professional_development_goal (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, goal VARCHAR(255) NOT NULL, progression INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_6CF7CF7FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professional_development_sub_goal (id INT AUTO_INCREMENT NOT NULL, goal_id INT DEFAULT NULL, sub_goal VARCHAR(255) NOT NULL, sub_goal_status TEXT NOT NULL, INDEX IDX_11C09C39667D1AFE (goal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sound_metric (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_D8C45B20A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tea_or_coffee_meeting (id INT AUTO_INCREMENT NOT NULL, initiator_id INT DEFAULT NULL, meeting_type TEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_F54538757DB3B714 (initiator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tea_or_coffee_meeting_user (id INT AUTO_INCREMENT NOT NULL, meeting_id INT DEFAULT NULL, invited_user_id INT DEFAULT NULL, invitation_status TEXT NOT NULL, INDEX IDX_CB81437067433D9C (meeting_id), INDEX IDX_CB814370C58DAD6E (invited_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, team_name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C4E0A61F979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE temperature_metric (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_8EAA1620A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, job_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, password VARCHAR(255) DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, INDEX IDX_8D93D649979B1AD6 (company_id), INDEX IDX_8D93D649BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_team (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, team_id INT DEFAULT NULL, INDEX IDX_BE61EAD6A76ED395 (user_id), INDEX IDX_BE61EAD6296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE daily_feedback ADD CONSTRAINT FK_6826938CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE daily_feedback ADD CONSTRAINT FK_6826938C296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE daily_feedback_team_preferences ADD CONSTRAINT FK_2FD835E296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE environment_metrics_preferences ADD CONSTRAINT FK_9053EF97A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE humidity_metric ADD CONSTRAINT FK_F8AA7F6EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE job_permission ADD CONSTRAINT FK_8A3E02B7BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE job_permission ADD CONSTRAINT FK_8A3E02B7FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id)');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE luminosity_metric ADD CONSTRAINT FK_FAED7C8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CACD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification_preferences ADD CONSTRAINT FK_3CAA95B4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE personal_feedback ADD CONSTRAINT FK_7E169A11F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE personal_feedback ADD CONSTRAINT FK_7E169A11CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE professional_development_goal ADD CONSTRAINT FK_6CF7CF7FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE professional_development_sub_goal ADD CONSTRAINT FK_11C09C39667D1AFE FOREIGN KEY (goal_id) REFERENCES professional_development_goal (id)');
        $this->addSql('ALTER TABLE sound_metric ADD CONSTRAINT FK_D8C45B20A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting ADD CONSTRAINT FK_F54538757DB3B714 FOREIGN KEY (initiator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting_user ADD CONSTRAINT FK_CB81437067433D9C FOREIGN KEY (meeting_id) REFERENCES tea_or_coffee_meeting (id)');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting_user ADD CONSTRAINT FK_CB814370C58DAD6E FOREIGN KEY (invited_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE temperature_metric ADD CONSTRAINT FK_8EAA1620A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE user_team ADD CONSTRAINT FK_BE61EAD6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_team ADD CONSTRAINT FK_BE61EAD6296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE daily_feedback DROP FOREIGN KEY FK_6826938CA76ED395');
        $this->addSql('ALTER TABLE daily_feedback DROP FOREIGN KEY FK_6826938C296CD8AE');
        $this->addSql('ALTER TABLE daily_feedback_team_preferences DROP FOREIGN KEY FK_2FD835E296CD8AE');
        $this->addSql('ALTER TABLE environment_metrics_preferences DROP FOREIGN KEY FK_9053EF97A76ED395');
        $this->addSql('ALTER TABLE humidity_metric DROP FOREIGN KEY FK_F8AA7F6EA76ED395');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8979B1AD6');
        $this->addSql('ALTER TABLE job_permission DROP FOREIGN KEY FK_8A3E02B7BE04EA9');
        $this->addSql('ALTER TABLE job_permission DROP FOREIGN KEY FK_8A3E02B7FED90CCA');
        $this->addSql('ALTER TABLE logs DROP FOREIGN KEY FK_F08FC65CA76ED395');
        $this->addSql('ALTER TABLE luminosity_metric DROP FOREIGN KEY FK_FAED7C8A76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAF624B39D');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CACD53EDB6');
        $this->addSql('ALTER TABLE notification_preferences DROP FOREIGN KEY FK_3CAA95B4A76ED395');
        $this->addSql('ALTER TABLE personal_feedback DROP FOREIGN KEY FK_7E169A11F624B39D');
        $this->addSql('ALTER TABLE personal_feedback DROP FOREIGN KEY FK_7E169A11CD53EDB6');
        $this->addSql('ALTER TABLE professional_development_goal DROP FOREIGN KEY FK_6CF7CF7FA76ED395');
        $this->addSql('ALTER TABLE professional_development_sub_goal DROP FOREIGN KEY FK_11C09C39667D1AFE');
        $this->addSql('ALTER TABLE sound_metric DROP FOREIGN KEY FK_D8C45B20A76ED395');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting DROP FOREIGN KEY FK_F54538757DB3B714');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting_user DROP FOREIGN KEY FK_CB81437067433D9C');
        $this->addSql('ALTER TABLE tea_or_coffee_meeting_user DROP FOREIGN KEY FK_CB814370C58DAD6E');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F979B1AD6');
        $this->addSql('ALTER TABLE temperature_metric DROP FOREIGN KEY FK_8EAA1620A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649979B1AD6');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BE04EA9');
        $this->addSql('ALTER TABLE user_team DROP FOREIGN KEY FK_BE61EAD6A76ED395');
        $this->addSql('ALTER TABLE user_team DROP FOREIGN KEY FK_BE61EAD6296CD8AE');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE daily_feedback');
        $this->addSql('DROP TABLE daily_feedback_team_preferences');
        $this->addSql('DROP TABLE environment_metrics_preferences');
        $this->addSql('DROP TABLE humidity_metric');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE job_permission');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE luminosity_metric');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notification_preferences');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE personal_feedback');
        $this->addSql('DROP TABLE professional_development_goal');
        $this->addSql('DROP TABLE professional_development_sub_goal');
        $this->addSql('DROP TABLE sound_metric');
        $this->addSql('DROP TABLE tea_or_coffee_meeting');
        $this->addSql('DROP TABLE tea_or_coffee_meeting_user');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE temperature_metric');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_team');
    }
}
