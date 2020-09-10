<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190920084816 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE model_parts (part_id VARCHAR(255) NOT NULL, model_id VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_570387094CE34BEC (part_id), INDEX IDX_570387097975B7E7 (model_id), PRIMARY KEY(part_id, model_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE model_parts ADD CONSTRAINT FK_570387094CE34BEC FOREIGN KEY (part_id) REFERENCES rebrickable_part (id)');
        $this->addSql('ALTER TABLE model_parts ADD CONSTRAINT FK_570387097975B7E7 FOREIGN KEY (model_id) REFERENCES ldraw_model (id)');

        $this->addSql('INSERT INTO model_parts (model_id, part_id) SELECT DISTINCT rp.model_id, rp.id FROM rebrickable_part rp WHERE rp.model_id IS NOT NULL');

        $this->addSql('ALTER TABLE rebrickable_part DROP FOREIGN KEY FK_38E480B27975B7E7');
        $this->addSql('DROP INDEX IDX_38E480B27975B7E7 ON rebrickable_part');
        $this->addSql('ALTER TABLE rebrickable_part DROP model_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE model_parts');
        $this->addSql('ALTER TABLE rebrickable_part ADD model_id VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE rebrickable_part ADD CONSTRAINT FK_38E480B27975B7E7 FOREIGN KEY (model_id) REFERENCES ldraw_model (id)');
        $this->addSql('CREATE INDEX IDX_38E480B27975B7E7 ON rebrickable_part (model_id)');
    }
}
