<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210501140345 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE api_keys (id INT AUTO_INCREMENT NOT NULL, client_id LONGTEXT NOT NULL, client_secret LONGTEXT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, email LONGTEXT NOT NULL, keycloak_id LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, username LONGTEXT DEFAULT NULL, last_login DATETIME DEFAULT NULL, first_name LONGTEXT DEFAULT NULL, last_name LONGTEXT DEFAULT NULL, register_id LONGTEXT DEFAULT NULL, keycloakGroup LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', uid LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE keycloak_groups_to_servers (id INT AUTO_INCREMENT NOT NULL, server_id INT NOT NULL, keycloak_group LONGTEXT NOT NULL, INDEX IDX_A15849ED1844E6B7 (server_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE license (id INT AUTO_INCREMENT NOT NULL, license_key LONGTEXT NOT NULL, license LONGTEXT NOT NULL, valid_until DATETIME NOT NULL, url LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rooms (id INT AUTO_INCREMENT NOT NULL, standort_id INT NOT NULL, moderator_id INT DEFAULT NULL, name LONGTEXT NOT NULL, start DATETIME DEFAULT NULL, enddate DATETIME DEFAULT NULL, uid LONGTEXT NOT NULL, duration DOUBLE PRECISION NOT NULL, sequence INT NOT NULL, uid_real LONGTEXT DEFAULT NULL, only_registered_users TINYINT(1) NOT NULL, agenda LONGTEXT DEFAULT NULL, dissallow_screenshare_global TINYINT(1) DEFAULT NULL, dissallow_private_message TINYINT(1) DEFAULT NULL, public TINYINT(1) DEFAULT NULL, show_room_on_joinpage TINYINT(1) DEFAULT NULL, uid_participant LONGTEXT DEFAULT NULL, uid_moderator LONGTEXT DEFAULT NULL, max_participants INT DEFAULT NULL, schedule_meeting TINYINT(1) DEFAULT NULL, waitinglist TINYINT(1) DEFAULT NULL, INDEX IDX_7CA11A96588CAAD (standort_id), INDEX IDX_7CA11A96D0AFA354 (moderator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rooms_user (rooms_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_EA64C2B48E2368AB (rooms_id), INDEX IDX_EA64C2B4A76ED395 (user_id), PRIMARY KEY(rooms_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scheduling (id INT AUTO_INCREMENT NOT NULL, room_id INT NOT NULL, uid LONGTEXT NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_FD931BF554177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scheduling_time (id INT AUTO_INCREMENT NOT NULL, scheduling_id INT NOT NULL, time DATETIME NOT NULL, INDEX IDX_6B3A7EB4157E7D92 (scheduling_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scheduling_time_user (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, schedule_time_id INT NOT NULL, accept INT DEFAULT NULL, INDEX IDX_11E40D03A76ED395 (user_id), INDEX IDX_11E40D03D380F18A (schedule_time_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE standort (id INT AUTO_INCREMENT NOT NULL, administrator_id INT NOT NULL, url LONGTEXT NOT NULL, app_id LONGTEXT DEFAULT NULL, app_secret LONGTEXT DEFAULT NULL, logo_url LONGTEXT DEFAULT NULL, smtp_host LONGTEXT DEFAULT NULL, smtp_port INT DEFAULT NULL, smtp_password LONGTEXT DEFAULT NULL, smtp_username LONGTEXT DEFAULT NULL, smtp_encryption LONGTEXT DEFAULT NULL, smtp_email LONGTEXT DEFAULT NULL, smtp_sender_name LONGTEXT DEFAULT NULL, slug LONGTEXT NOT NULL, privacy_policy LONGTEXT DEFAULT NULL, license_key LONGTEXT DEFAULT NULL, api_key LONGTEXT DEFAULT NULL, static_background_color VARCHAR(7) DEFAULT NULL, show_static_background_color TINYINT(1) DEFAULT NULL, feature_enable_by_jwt TINYINT(1) DEFAULT NULL, server_email_header LONGTEXT DEFAULT NULL, server_email_body LONGTEXT DEFAULT NULL, INDEX IDX_7DEEAE94B09E92C (administrator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE standort_user (standort_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_84E62C5D588CAAD (standort_id), INDEX IDX_84E62C5DA76ED395 (user_id), PRIMARY KEY(standort_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriber (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, room_id INT NOT NULL, uid LONGTEXT NOT NULL, INDEX IDX_AD005B69A76ED395 (user_id), INDEX IDX_AD005B6954177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE userRoomsAttributes (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, room_id INT NOT NULL, share_display TINYINT(1) DEFAULT NULL, moderator TINYINT(1) DEFAULT NULL, private_message TINYINT(1) DEFAULT NULL, INDEX IDX_F98B4CE4A76ED395 (user_id), INDEX IDX_F98B4CE454177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE waitinglist (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, room_id INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_382FDA02A76ED395 (user_id), INDEX IDX_382FDA0254177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE keycloak_groups_to_servers ADD CONSTRAINT FK_A15849ED1844E6B7 FOREIGN KEY (server_id) REFERENCES standort (id)');
        $this->addSql('ALTER TABLE rooms ADD CONSTRAINT FK_7CA11A96588CAAD FOREIGN KEY (standort_id) REFERENCES standort (id)');
        $this->addSql('ALTER TABLE rooms ADD CONSTRAINT FK_7CA11A96D0AFA354 FOREIGN KEY (moderator_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE rooms_user ADD CONSTRAINT FK_EA64C2B48E2368AB FOREIGN KEY (rooms_id) REFERENCES rooms (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rooms_user ADD CONSTRAINT FK_EA64C2B4A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scheduling ADD CONSTRAINT FK_FD931BF554177093 FOREIGN KEY (room_id) REFERENCES rooms (id)');
        $this->addSql('ALTER TABLE scheduling_time ADD CONSTRAINT FK_6B3A7EB4157E7D92 FOREIGN KEY (scheduling_id) REFERENCES scheduling (id)');
        $this->addSql('ALTER TABLE scheduling_time_user ADD CONSTRAINT FK_11E40D03A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE scheduling_time_user ADD CONSTRAINT FK_11E40D03D380F18A FOREIGN KEY (schedule_time_id) REFERENCES scheduling_time (id)');
        $this->addSql('ALTER TABLE standort ADD CONSTRAINT FK_7DEEAE94B09E92C FOREIGN KEY (administrator_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE standort_user ADD CONSTRAINT FK_84E62C5D588CAAD FOREIGN KEY (standort_id) REFERENCES standort (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE standort_user ADD CONSTRAINT FK_84E62C5DA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscriber ADD CONSTRAINT FK_AD005B69A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE subscriber ADD CONSTRAINT FK_AD005B6954177093 FOREIGN KEY (room_id) REFERENCES rooms (id)');
        $this->addSql('ALTER TABLE userRoomsAttributes ADD CONSTRAINT FK_F98B4CE4A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE userRoomsAttributes ADD CONSTRAINT FK_F98B4CE454177093 FOREIGN KEY (room_id) REFERENCES rooms (id)');
        $this->addSql('ALTER TABLE waitinglist ADD CONSTRAINT FK_382FDA02A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE waitinglist ADD CONSTRAINT FK_382FDA0254177093 FOREIGN KEY (room_id) REFERENCES rooms (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80233D34C1');
        $this->addSql('ALTER TABLE rooms DROP FOREIGN KEY FK_7CA11A96D0AFA354');
        $this->addSql('ALTER TABLE rooms_user DROP FOREIGN KEY FK_EA64C2B4A76ED395');
        $this->addSql('ALTER TABLE scheduling_time_user DROP FOREIGN KEY FK_11E40D03A76ED395');
        $this->addSql('ALTER TABLE standort DROP FOREIGN KEY FK_7DEEAE94B09E92C');
        $this->addSql('ALTER TABLE standort_user DROP FOREIGN KEY FK_84E62C5DA76ED395');
        $this->addSql('ALTER TABLE subscriber DROP FOREIGN KEY FK_AD005B69A76ED395');
        $this->addSql('ALTER TABLE userRoomsAttributes DROP FOREIGN KEY FK_F98B4CE4A76ED395');
        $this->addSql('ALTER TABLE waitinglist DROP FOREIGN KEY FK_382FDA02A76ED395');
        $this->addSql('ALTER TABLE rooms_user DROP FOREIGN KEY FK_EA64C2B48E2368AB');
        $this->addSql('ALTER TABLE scheduling DROP FOREIGN KEY FK_FD931BF554177093');
        $this->addSql('ALTER TABLE subscriber DROP FOREIGN KEY FK_AD005B6954177093');
        $this->addSql('ALTER TABLE userRoomsAttributes DROP FOREIGN KEY FK_F98B4CE454177093');
        $this->addSql('ALTER TABLE waitinglist DROP FOREIGN KEY FK_382FDA0254177093');
        $this->addSql('ALTER TABLE scheduling_time DROP FOREIGN KEY FK_6B3A7EB4157E7D92');
        $this->addSql('ALTER TABLE scheduling_time_user DROP FOREIGN KEY FK_11E40D03D380F18A');
        $this->addSql('ALTER TABLE keycloak_groups_to_servers DROP FOREIGN KEY FK_A15849ED1844E6B7');
        $this->addSql('ALTER TABLE rooms DROP FOREIGN KEY FK_7CA11A96588CAAD');
        $this->addSql('ALTER TABLE standort_user DROP FOREIGN KEY FK_84E62C5D588CAAD');
        $this->addSql('DROP TABLE api_keys');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('DROP TABLE keycloak_groups_to_servers');
        $this->addSql('DROP TABLE license');
        $this->addSql('DROP TABLE rooms');
        $this->addSql('DROP TABLE rooms_user');
        $this->addSql('DROP TABLE scheduling');
        $this->addSql('DROP TABLE scheduling_time');
        $this->addSql('DROP TABLE scheduling_time_user');
        $this->addSql('DROP TABLE standort');
        $this->addSql('DROP TABLE standort_user');
        $this->addSql('DROP TABLE subscriber');
        $this->addSql('DROP TABLE userRoomsAttributes');
        $this->addSql('DROP TABLE waitinglist');
    }
}
