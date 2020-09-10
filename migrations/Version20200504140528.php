<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504140528 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ldraw_relation (id INT AUTO_INCREMENT NOT NULL, parent_id VARCHAR(255) DEFAULT NULL, child_id VARCHAR(255) DEFAULT NULL, relation_type VARCHAR(255) NOT NULL, INDEX IDX_5E2709FE727ACA70 (parent_id), INDEX IDX_5E2709FEDD62C21B (child_id), UNIQUE INDEX UNIQ_5E2709FE727ACA70DD62C21B (parent_id, child_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ldraw_relation ADD CONSTRAINT FK_5E2709FE727ACA70 FOREIGN KEY (parent_id) REFERENCES ldraw_model (id)');
        $this->addSql('ALTER TABLE ldraw_relation ADD CONSTRAINT FK_5E2709FEDD62C21B FOREIGN KEY (child_id) REFERENCES ldraw_model (id)');
        $this->addSql('ALTER TABLE ldraw_model CHANGE type type VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE ldraw_alias DROP FOREIGN KEY FK_D80892617975B7E7');
        $this->addSql('DROP INDEX IDX_D80892617975B7E7 ON ldraw_alias');
        $this->addSql('ALTER TABLE ldraw_alias DROP model_id, CHANGE id id INT NOT NULL, CHANGE type type VARCHAR(1) NOT NULL');
        $this->addSql('ALTER TABLE ldraw_alias ADD CONSTRAINT FK_D8089261BF396750 FOREIGN KEY (id) REFERENCES ldraw_relation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ldraw_subpart DROP FOREIGN KEY FK_3D106469727ACA70');
        $this->addSql('ALTER TABLE ldraw_subpart DROP FOREIGN KEY FK_3D106469781E323');
        $this->addSql('DROP INDEX IDX_3D106469727ACA70 ON ldraw_subpart');
        $this->addSql('DROP INDEX IDX_3D106469781E323 ON ldraw_subpart');
        $this->addSql('ALTER TABLE ldraw_subpart DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE ldraw_subpart ADD id INT NOT NULL, DROP parent_id, DROP subpart_id, CHANGE color_id color_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ldraw_subpart ADD CONSTRAINT FK_3D106469BF396750 FOREIGN KEY (id) REFERENCES ldraw_relation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ldraw_subpart ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ldraw_alias DROP FOREIGN KEY FK_D8089261BF396750');
        $this->addSql('ALTER TABLE ldraw_subpart DROP FOREIGN KEY FK_3D106469BF396750');
        $this->addSql('DROP TABLE ldraw_relation');
        $this->addSql('ALTER TABLE ldraw_alias ADD model_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE id id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE type type VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ldraw_alias ADD CONSTRAINT FK_D80892617975B7E7 FOREIGN KEY (model_id) REFERENCES ldraw_model (id)');
        $this->addSql('CREATE INDEX IDX_D80892617975B7E7 ON ldraw_alias (model_id)');
        $this->addSql('ALTER TABLE ldraw_model CHANGE type type VARCHAR(15) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ldraw_subpart DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE ldraw_subpart ADD parent_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD subpart_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP id, CHANGE color_id color_id INT NOT NULL');
        $this->addSql('ALTER TABLE ldraw_subpart ADD CONSTRAINT FK_3D106469727ACA70 FOREIGN KEY (parent_id) REFERENCES ldraw_model (id)');
        $this->addSql('ALTER TABLE ldraw_subpart ADD CONSTRAINT FK_3D106469781E323 FOREIGN KEY (subpart_id) REFERENCES ldraw_model (id)');
        $this->addSql('CREATE INDEX IDX_3D106469727ACA70 ON ldraw_subpart (parent_id)');
        $this->addSql('CREATE INDEX IDX_3D106469781E323 ON ldraw_subpart (subpart_id)');
        $this->addSql('ALTER TABLE ldraw_subpart ADD PRIMARY KEY (parent_id, subpart_id, color_id)');
    }
}
