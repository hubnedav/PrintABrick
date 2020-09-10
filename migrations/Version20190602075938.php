<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190602075938 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Update Rebrickable tables to match csv files';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rebrickable_part ADD material INT NOT NULL');
        $this->addSql('CREATE TABLE rebrickable_part_relationships (parent_id VARCHAR(255) NOT NULL, children_id VARCHAR(255) NOT NULL, type VARCHAR(1) NOT NULL, INDEX IDX_D233F0AB727ACA70 (parent_id), INDEX IDX_D233F0AB3D3D2749 (children_id), PRIMARY KEY(parent_id, children_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rebrickable_part_relationships ADD CONSTRAINT FK_D233F0AB727ACA70 FOREIGN KEY (parent_id) REFERENCES rebrickable_part (id)');
        $this->addSql('ALTER TABLE rebrickable_part_relationships ADD CONSTRAINT FK_D233F0AB3D3D2749 FOREIGN KEY (children_id) REFERENCES rebrickable_part (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rebrickable_part DROP material');
        $this->addSql('DROP TABLE rebrickable_part_relationships');
    }
}
