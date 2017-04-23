<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170423104850 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE color (id INT NOT NULL, rgb VARCHAR(6) NOT NULL, transparent TINYINT(1) NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ldraw_alias (id VARCHAR(255) NOT NULL, model_id VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D8089261BF396750 (id), INDEX IDX_D80892617975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ldraw_author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_7041BAA25E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ldraw_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_3AE257765E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ldraw_keyword (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_F739171F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ldraw_model (id VARCHAR(255) NOT NULL, category_id INT DEFAULT NULL, author_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, path VARCHAR(255) DEFAULT NULL, modified DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_EEF18B2CBF396750 (id), INDEX IDX_EEF18B2C12469DE2 (category_id), INDEX IDX_EEF18B2CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE model_keyword (model_id VARCHAR(255) NOT NULL, keyword_id INT NOT NULL, INDEX IDX_3A0018CA7975B7E7 (model_id), INDEX IDX_3A0018CA115D4552 (keyword_id), PRIMARY KEY(model_id, keyword_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ldraw_subpart (parent_id VARCHAR(255) NOT NULL, subpart_id VARCHAR(255) NOT NULL, color_id INT NOT NULL, count INT NOT NULL, INDEX IDX_3D106469727ACA70 (parent_id), INDEX IDX_3D106469781E323 (subpart_id), INDEX IDX_3D1064697ADA1FB5 (color_id), PRIMARY KEY(parent_id, subpart_id, color_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rebrickable_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rebrickable_inventory (id INT AUTO_INCREMENT NOT NULL, set_id VARCHAR(255) DEFAULT NULL, version INT NOT NULL, INDEX IDX_A88A6D6810FB0D18 (set_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rebrickable_inventory_parts (color_id INT NOT NULL, spare TINYINT(1) NOT NULL, part_id VARCHAR(255) NOT NULL, inventory_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_33F22F2E7ADA1FB5 (color_id), INDEX IDX_33F22F2E4CE34BEC (part_id), INDEX IDX_33F22F2E9EEA759 (inventory_id), PRIMARY KEY(color_id, spare, part_id, inventory_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rebrickable_inventory_sets (inventory_id INT NOT NULL, set_id VARCHAR(255) NOT NULL, quantity INT NOT NULL, INDEX IDX_23DA8E2F9EEA759 (inventory_id), INDEX IDX_23DA8E2F10FB0D18 (set_id), PRIMARY KEY(inventory_id, set_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rebrickable_part (id VARCHAR(255) NOT NULL, category_id INT DEFAULT NULL, model_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_38E480B2BF396750 (id), INDEX IDX_38E480B212469DE2 (category_id), INDEX IDX_38E480B27975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rebrickable_set (id VARCHAR(255) NOT NULL, theme_id INT DEFAULT NULL, year INT DEFAULT NULL, num_parts INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_BC61D898BF396750 (id), INDEX IDX_BC61D89859027487 (theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rebrickable_theme (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_C06CB9DD727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ldraw_alias ADD CONSTRAINT FK_D80892617975B7E7 FOREIGN KEY (model_id) REFERENCES ldraw_model (id)');
        $this->addSql('ALTER TABLE ldraw_model ADD CONSTRAINT FK_EEF18B2C12469DE2 FOREIGN KEY (category_id) REFERENCES ldraw_category (id)');
        $this->addSql('ALTER TABLE ldraw_model ADD CONSTRAINT FK_EEF18B2CF675F31B FOREIGN KEY (author_id) REFERENCES ldraw_author (id)');
        $this->addSql('ALTER TABLE model_keyword ADD CONSTRAINT FK_3A0018CA7975B7E7 FOREIGN KEY (model_id) REFERENCES ldraw_model (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE model_keyword ADD CONSTRAINT FK_3A0018CA115D4552 FOREIGN KEY (keyword_id) REFERENCES ldraw_keyword (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ldraw_subpart ADD CONSTRAINT FK_3D106469727ACA70 FOREIGN KEY (parent_id) REFERENCES ldraw_model (id)');
        $this->addSql('ALTER TABLE ldraw_subpart ADD CONSTRAINT FK_3D106469781E323 FOREIGN KEY (subpart_id) REFERENCES ldraw_model (id)');
        $this->addSql('ALTER TABLE ldraw_subpart ADD CONSTRAINT FK_3D1064697ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id)');
        $this->addSql('ALTER TABLE rebrickable_inventory ADD CONSTRAINT FK_A88A6D6810FB0D18 FOREIGN KEY (set_id) REFERENCES rebrickable_set (id)');
        $this->addSql('ALTER TABLE rebrickable_inventory_parts ADD CONSTRAINT FK_33F22F2E7ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id)');
        $this->addSql('ALTER TABLE rebrickable_inventory_parts ADD CONSTRAINT FK_33F22F2E4CE34BEC FOREIGN KEY (part_id) REFERENCES rebrickable_part (id)');
        $this->addSql('ALTER TABLE rebrickable_inventory_parts ADD CONSTRAINT FK_33F22F2E9EEA759 FOREIGN KEY (inventory_id) REFERENCES rebrickable_inventory (id)');
        $this->addSql('ALTER TABLE rebrickable_inventory_sets ADD CONSTRAINT FK_23DA8E2F9EEA759 FOREIGN KEY (inventory_id) REFERENCES rebrickable_inventory (id)');
        $this->addSql('ALTER TABLE rebrickable_inventory_sets ADD CONSTRAINT FK_23DA8E2F10FB0D18 FOREIGN KEY (set_id) REFERENCES rebrickable_set (id)');
        $this->addSql('ALTER TABLE rebrickable_part ADD CONSTRAINT FK_38E480B212469DE2 FOREIGN KEY (category_id) REFERENCES rebrickable_category (id)');
        $this->addSql('ALTER TABLE rebrickable_part ADD CONSTRAINT FK_38E480B27975B7E7 FOREIGN KEY (model_id) REFERENCES ldraw_model (id)');
        $this->addSql('ALTER TABLE rebrickable_set ADD CONSTRAINT FK_BC61D89859027487 FOREIGN KEY (theme_id) REFERENCES rebrickable_theme (id)');
        $this->addSql('ALTER TABLE rebrickable_theme ADD CONSTRAINT FK_C06CB9DD727ACA70 FOREIGN KEY (parent_id) REFERENCES rebrickable_theme (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ldraw_subpart DROP FOREIGN KEY FK_3D1064697ADA1FB5');
        $this->addSql('ALTER TABLE rebrickable_inventory_parts DROP FOREIGN KEY FK_33F22F2E7ADA1FB5');
        $this->addSql('ALTER TABLE ldraw_model DROP FOREIGN KEY FK_EEF18B2CF675F31B');
        $this->addSql('ALTER TABLE ldraw_model DROP FOREIGN KEY FK_EEF18B2C12469DE2');
        $this->addSql('ALTER TABLE model_keyword DROP FOREIGN KEY FK_3A0018CA115D4552');
        $this->addSql('ALTER TABLE ldraw_alias DROP FOREIGN KEY FK_D80892617975B7E7');
        $this->addSql('ALTER TABLE model_keyword DROP FOREIGN KEY FK_3A0018CA7975B7E7');
        $this->addSql('ALTER TABLE ldraw_subpart DROP FOREIGN KEY FK_3D106469727ACA70');
        $this->addSql('ALTER TABLE ldraw_subpart DROP FOREIGN KEY FK_3D106469781E323');
        $this->addSql('ALTER TABLE rebrickable_part DROP FOREIGN KEY FK_38E480B27975B7E7');
        $this->addSql('ALTER TABLE rebrickable_part DROP FOREIGN KEY FK_38E480B212469DE2');
        $this->addSql('ALTER TABLE rebrickable_inventory_parts DROP FOREIGN KEY FK_33F22F2E9EEA759');
        $this->addSql('ALTER TABLE rebrickable_inventory_sets DROP FOREIGN KEY FK_23DA8E2F9EEA759');
        $this->addSql('ALTER TABLE rebrickable_inventory_parts DROP FOREIGN KEY FK_33F22F2E4CE34BEC');
        $this->addSql('ALTER TABLE rebrickable_inventory DROP FOREIGN KEY FK_A88A6D6810FB0D18');
        $this->addSql('ALTER TABLE rebrickable_inventory_sets DROP FOREIGN KEY FK_23DA8E2F10FB0D18');
        $this->addSql('ALTER TABLE rebrickable_set DROP FOREIGN KEY FK_BC61D89859027487');
        $this->addSql('ALTER TABLE rebrickable_theme DROP FOREIGN KEY FK_C06CB9DD727ACA70');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE ldraw_alias');
        $this->addSql('DROP TABLE ldraw_author');
        $this->addSql('DROP TABLE ldraw_category');
        $this->addSql('DROP TABLE ldraw_keyword');
        $this->addSql('DROP TABLE ldraw_model');
        $this->addSql('DROP TABLE model_keyword');
        $this->addSql('DROP TABLE ldraw_subpart');
        $this->addSql('DROP TABLE rebrickable_category');
        $this->addSql('DROP TABLE rebrickable_inventory');
        $this->addSql('DROP TABLE rebrickable_inventory_parts');
        $this->addSql('DROP TABLE rebrickable_inventory_sets');
        $this->addSql('DROP TABLE rebrickable_part');
        $this->addSql('DROP TABLE rebrickable_set');
        $this->addSql('DROP TABLE rebrickable_theme');
    }
}
