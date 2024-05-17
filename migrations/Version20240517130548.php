<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517130548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE favorite_recipe');
        $this->addSql('DROP TABLE favorite_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL)');
        $this->addSql('CREATE TABLE favorite_recipe (favorite_id INTEGER NOT NULL, recipe_id INTEGER NOT NULL, PRIMARY KEY(favorite_id, recipe_id), CONSTRAINT FK_E63DDDCDAA17481D FOREIGN KEY (favorite_id) REFERENCES favorite (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E63DDDCD59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E63DDDCD59D8A214 ON favorite_recipe (recipe_id)');
        $this->addSql('CREATE INDEX IDX_E63DDDCDAA17481D ON favorite_recipe (favorite_id)');
        $this->addSql('CREATE TABLE favorite_user (favorite_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(favorite_id, user_id), CONSTRAINT FK_6395CF76AA17481D FOREIGN KEY (favorite_id) REFERENCES favorite (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6395CF76A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6395CF76A76ED395 ON favorite_user (user_id)');
        $this->addSql('CREATE INDEX IDX_6395CF76AA17481D ON favorite_user (favorite_id)');
    }
}
