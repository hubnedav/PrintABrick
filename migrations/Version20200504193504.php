<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504193504 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ldraw_alias');
        $this->addSql('DROP TABLE ldraw_subpart');
        $this->addSql('ALTER TABLE ldraw_relation ADD color_id INT DEFAULT NULL, ADD count INT DEFAULT NULL, ADD type VARCHAR(1) DEFAULT NULL, CHANGE parent_id parent_id VARCHAR(255) NOT NULL, CHANGE child_id child_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE ldraw_relation ADD CONSTRAINT FK_5E2709FE7ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id)');
        $this->addSql('CREATE INDEX IDX_5E2709FE7ADA1FB5 ON ldraw_relation (color_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ldraw_alias (id INT NOT NULL, type VARCHAR(1) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE ldraw_subpart (id INT NOT NULL, color_id INT DEFAULT NULL, count INT NOT NULL, INDEX IDX_3D1064697ADA1FB5 (color_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ldraw_alias ADD CONSTRAINT FK_D8089261BF396750 FOREIGN KEY (id) REFERENCES ldraw_relation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ldraw_subpart ADD CONSTRAINT FK_3D1064697ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id)');
        $this->addSql('ALTER TABLE ldraw_subpart ADD CONSTRAINT FK_3D106469BF396750 FOREIGN KEY (id) REFERENCES ldraw_relation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ldraw_relation DROP FOREIGN KEY FK_5E2709FE7ADA1FB5');
        $this->addSql('DROP INDEX IDX_5E2709FE7ADA1FB5 ON ldraw_relation');
        $this->addSql('ALTER TABLE ldraw_relation DROP color_id, DROP count, DROP type, CHANGE parent_id parent_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE child_id child_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
