<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210920135444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE free_fields_user_answer (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, free_field_id INT NOT NULL, answer LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_1DFDD1D0A76ED395 (user_id), INDEX IDX_1DFDD1D0F3A0FF5A (free_field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE free_fields_user_answer ADD CONSTRAINT FK_1DFDD1D0A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE free_fields_user_answer ADD CONSTRAINT FK_1DFDD1D0F3A0FF5A FOREIGN KEY (free_field_id) REFERENCES free_field (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE free_fields_user_answer');
    }
}
