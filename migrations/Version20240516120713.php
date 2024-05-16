<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516120713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe ADD COLUMN slug VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__recipe AS SELECT id, name, image, recipe_text, duration, people FROM recipe');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('CREATE TABLE recipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, recipe_text CLOB NOT NULL, duration VARCHAR(255) NOT NULL, people INTEGER NOT NULL)');
        $this->addSql('INSERT INTO recipe (id, name, image, recipe_text, duration, people) SELECT id, name, image, recipe_text, duration, people FROM __temp__recipe');
        $this->addSql('DROP TABLE __temp__recipe');
    }
}
