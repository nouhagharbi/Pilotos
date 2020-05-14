<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200512115555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE action_plan (id INT AUTO_INCREMENT NOT NULL, origin VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objective (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, timelimit DATETIME NOT NULL, predefined_indicator TINYINT(1) NOT NULL, performance_indicator VARCHAR(255) NOT NULL, objective_to_acheive VARCHAR(255) NOT NULL, initial_state_indicator VARCHAR(255) NOT NULL, action_number INT NOT NULL, current_state_indicator VARCHAR(255) NOT NULL, advancement VARCHAR(255) NOT NULL, current_state VARCHAR(255) NOT NULL, comments VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objective_enjeu (objective_id INT NOT NULL, enjeu_id INT NOT NULL, INDEX IDX_5738E68473484933 (objective_id), INDEX IDX_5738E6841BFCDE83 (enjeu_id), PRIMARY KEY(objective_id, enjeu_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objective_process (objective_id INT NOT NULL, process_id INT NOT NULL, INDEX IDX_2A1E572373484933 (objective_id), INDEX IDX_2A1E57237EC2F574 (process_id), PRIMARY KEY(objective_id, process_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE process (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, performance_indicator VARCHAR(255) NOT NULL, pilot VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE objective_enjeu ADD CONSTRAINT FK_5738E68473484933 FOREIGN KEY (objective_id) REFERENCES objective (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objective_enjeu ADD CONSTRAINT FK_5738E6841BFCDE83 FOREIGN KEY (enjeu_id) REFERENCES enjeu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objective_process ADD CONSTRAINT FK_2A1E572373484933 FOREIGN KEY (objective_id) REFERENCES objective (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objective_process ADD CONSTRAINT FK_2A1E57237EC2F574 FOREIGN KEY (process_id) REFERENCES process (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE access_token CHANGE user_id user_id INT DEFAULT NULL, CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE refresh_token CHANGE user_id user_id INT DEFAULT NULL, CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE auth_code CHANGE user_id user_id INT DEFAULT NULL, CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE objective_enjeu DROP FOREIGN KEY FK_5738E68473484933');
        $this->addSql('ALTER TABLE objective_process DROP FOREIGN KEY FK_2A1E572373484933');
        $this->addSql('ALTER TABLE objective_process DROP FOREIGN KEY FK_2A1E57237EC2F574');
        $this->addSql('DROP TABLE action_plan');
        $this->addSql('DROP TABLE objective');
        $this->addSql('DROP TABLE objective_enjeu');
        $this->addSql('DROP TABLE objective_process');
        $this->addSql('DROP TABLE process');
        $this->addSql('ALTER TABLE access_token CHANGE user_id user_id INT DEFAULT NULL, CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE auth_code CHANGE user_id user_id INT DEFAULT NULL, CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE refresh_token CHANGE user_id user_id INT DEFAULT NULL, CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE salt salt VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE reset_token reset_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
