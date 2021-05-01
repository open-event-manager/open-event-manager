<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210501145354 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE keycloak_groups_to_standorts (id INT AUTO_INCREMENT NOT NULL, standort_id INT NOT NULL, keycloak_group LONGTEXT NOT NULL, INDEX IDX_B025A093588CAAD (standort_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE keycloak_groups_to_standorts ADD CONSTRAINT FK_B025A093588CAAD FOREIGN KEY (standort_id) REFERENCES standort (id)');
        $this->addSql('DROP TABLE keycloak_groups_to_servers');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE keycloak_groups_to_servers (id INT AUTO_INCREMENT NOT NULL, server_id INT NOT NULL, keycloak_group LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_A15849ED1844E6B7 (server_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE keycloak_groups_to_servers ADD CONSTRAINT FK_A15849ED1844E6B7 FOREIGN KEY (server_id) REFERENCES standort (id)');
        $this->addSql('DROP TABLE keycloak_groups_to_standorts');
    }
}
