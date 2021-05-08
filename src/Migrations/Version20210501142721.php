<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210501142721 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE standort ADD street LONGTEXT DEFAULT NULL, ADD number LONGTEXT DEFAULT NULL, ADD roomnumber LONGTEXT DEFAULT NULL, ADD plz LONGTEXT NOT NULL, ADD city LONGTEXT NOT NULL, ADD directions LONGTEXT DEFAULT NULL, DROP app_id, DROP app_secret, CHANGE url name LONGTEXT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE standort ADD url LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD app_id LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD app_secret LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP name, DROP street, DROP number, DROP roomnumber, DROP plz, DROP city, DROP directions');
    }
}
