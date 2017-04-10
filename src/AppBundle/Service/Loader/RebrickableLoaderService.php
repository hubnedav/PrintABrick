<?php

namespace AppBundle\Service\Loader;

use AppBundle\Entity\Rebrickable\Set;

//TODO Refactor + validate csv files
class RebrickableLoaderService extends BaseLoaderService
{
    private $rebrickable_url;

    /**
     * ModelLoaderService constructor.
     */
    public function __construct($rebrickable_url)
    {
        $this->rebrickable_url = $rebrickable_url;
    }

    public function loadTables()
    {
        $connection = $this->em->getConnection();

        try {
            $connection->beginTransaction();
            $connection->prepare('SET foreign_key_checks = 0;')->execute();

            $this->truncateTables();
            $this->loadColorTable();
            $this->loadPartTable();
            $this->loadCategoryTable();
            $this->loadThemeTable();
            $this->loadSetTable();
            $this->loadInventoryTable();
            $this->loadInventoryPartTable();
            $this->loadInventorySetTable();

            $connection->prepare('SET foreign_key_checks = 1;')->execute();
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    private function truncateTables()
    {
        $query =
            'TRUNCATE TABLE rebrickable_inventory_parts;
            TRUNCATE TABLE rebrickable_color;
            TRUNCATE TABLE rebrickable_inventory;
            TRUNCATE TABLE rebrickable_set;
            TRUNCATE TABLE rebrickable_theme;
            TRUNCATE TABLE rebrickable_part;
            TRUNCATE TABLE rebrickable_category;
           ';

        return $this->em->getConnection()->prepare($query)->execute();
    }

    private function loadCsvFile($file, $table, $columns)
    {
        $query = sprintf("LOAD DATA LOCAL INFILE '%s' 
            REPLACE INTO TABLE %s
            FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
            LINES TERMINATED BY '\\n'
            IGNORE 1 LINES %s", addslashes($file), $table, $columns);

        return $this->em->getConnection()->prepare($query)->execute();
    }

    private function loadInventoryTable()
    {
        $file = $this->downloadFile($this->rebrickable_url.'inventories.csv');

        return $this->loadCsvFile($file, 'rebrickable_inventory', '(`id`,`version`,`set_id`)');
    }

    private function loadInventoryPartTable()
    {
        $file = $this->downloadFile($this->rebrickable_url.'inventory_parts.csv');

        return $this->loadCsvFile($file, 'rebrickable_inventory_parts', '(`inventory_id`,`part_id`,`color_id`,`quantity`, @var) SET spare = IF(@var=\'t\',1,0)');
    }

    private function loadInventorySetTable()
    {
        $file = $this->downloadFile($this->rebrickable_url.'inventory_sets.csv');

        return $this->loadCsvFile($file, 'rebrickable_inventory_sets', '(`inventory_id`,`set_id`,`quantity`)');
    }

    private function loadSetTable()
    {
        $file = $this->downloadFile($this->rebrickable_url.'sets.csv');

        return $this->loadCsvFile($file, 'rebrickable_set', '(`id`,`name`,`year`,`theme_id`,`num_parts`)');
    }

    private function loadThemeTable()
    {
        $file = $this->downloadFile($this->rebrickable_url.'themes.csv');

        return $this->loadCsvFile($file, 'rebrickable_theme', '(`id`,`name`,@var) SET parent_id = nullif(@var,\'\')');
    }

    private function loadPartTable()
    {
        $file = $this->downloadFile($this->rebrickable_url.'parts.csv');

        return $this->loadCsvFile($file, 'rebrickable_part', '(`id`,`name`,`category_id`)');
    }

    private function loadCategoryTable()
    {
        $file = $this->downloadFile($this->rebrickable_url.'part_categories.csv');

        return $this->loadCsvFile($file, 'rebrickable_category', '(`id`,`name`)');
    }

    private function loadColorTable()
    {
        $file = $this->downloadFile($this->rebrickable_url.'colors.csv');

        return $this->loadCsvFile($file, 'rebrickable_color', '(`id`,`name`,`rgb`, @var) SET transparent = IF(@var=\'t\',1,0)');
    }
}
