<?php

namespace AppBundle\Service\Loader;

use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use Doctrine\ORM\Query\Expr\Join;

//TODO Refactor + validate csv files
class RebrickableLoader extends BaseLoader
{
    private $rebrickable_url;

    private $csvFile;

    /**
     * ModelLoaderService constructor.
     */
    public function __construct($rebrickable_url)
    {
        $this->rebrickable_url = $rebrickable_url;
    }

    public function loadAll()
    {
        $connection = $this->em->getConnection();
        $connection->beginTransaction();

        try {
            $this->loadCsvFiles();

            $connection->prepare('SET foreign_key_checks = 0;')->execute();
            $this->truncateTables();
            $connection->prepare('SET foreign_key_checks = 1;')->execute();

            $this->writeOutput([
                '<info>Truncated</info> <comment>rebrickable</comment> <info>database tables.</info>',
                'Loading CSV files into database...',
            ]);

            $this->loadColorTable($this->csvFile['colors']);
            $this->loadCategoryTable($this->csvFile['part_categories']);
            $this->loadPartTable($this->csvFile['parts']);
            $this->loadThemeTable($this->csvFile['themes']);
            $this->loadSetTable($this->csvFile['sets']);
            $this->loadInventoryTable($this->csvFile['inventories']);
            $this->loadInventorySetTable($this->csvFile['inventory_sets']);

            $connection->prepare('SET foreign_key_checks = 0;')->execute();
            $this->loadInventoryPartTable($this->csvFile['inventory_parts']);
            $connection->prepare('SET foreign_key_checks = 1;')->execute();
            $this->addMissingParts();

            $connection->commit();

            $this->writeOutput(['Rebrickable database loaded successfully!']);
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    private function loadCSVFiles()
    {
        $array = ['inventories', 'inventory_parts', 'inventory_sets', 'sets', 'themes', 'parts', 'part_categories', 'colors'];

        $this->writeOutput([
            '<fg=cyan>------------------------------------------------------------------------------</>',
            '<fg=cyan>Loading Rebrickable CSV files</>',
            '<fg=cyan>------------------------------------------------------------------------------</>',
        ]);

        foreach ($array as $item) {
            $this->csvFile[$item] = $this->downloadFile($this->rebrickable_url.$item.'.csv');
        }

        $this->writeOutput(['<info>All CSV files loaded.</info>']);
    }

    private function truncateTables()
    {
        $query = '
            TRUNCATE TABLE rebrickable_inventory_parts;
            TRUNCATE TABLE rebrickable_inventory_sets;
            TRUNCATE TABLE rebrickable_inventory;
            TRUNCATE TABLE rebrickable_set;
            TRUNCATE TABLE rebrickable_theme;
            TRUNCATE TABLE rebrickable_part;
            TRUNCATE TABLE rebrickable_category;
            TRUNCATE TABLE rebrickable_color;
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

    private function addMissingParts()
    {
        $connection = $this->em->getConnection();
        $statement = $connection->prepare(
            'SELECT DISTINCT rebrickable_inventory_parts.part_id FROM rebrickable_inventory_parts
                 LEFT JOIN rebrickable_part ON rebrickable_inventory_parts.part_id = rebrickable_part.id
                 WHERE rebrickable_part.id IS NULL');
        $statement->execute();
        $foreignKeys = $statement->fetchAll();

        foreach ($foreignKeys as $foreignKey) {
            $part = new Part();
            $part->setNumber($foreignKey['part_id']);
            $this->em->getRepository(Part::class)->save($part);
        }
    }

    private function loadInventoryTable($csv)
    {
        return $this->loadCsvFile($csv, 'rebrickable_inventory', '(`id`,`version`,`set_id`)');
    }

    private function loadInventoryPartTable($csv)
    {
        return $this->loadCsvFile($csv, 'rebrickable_inventory_parts', '(`inventory_id`,`part_id`,`color_id`,`quantity`, @var) SET spare = IF(@var=\'t\',1,0)');
    }

    private function loadInventorySetTable($csv)
    {
        return $this->loadCsvFile($csv, 'rebrickable_inventory_sets', '(`inventory_id`,`set_id`,`quantity`)');
    }

    private function loadSetTable($csv)
    {
        return $this->loadCsvFile($csv, 'rebrickable_set', '(`id`,`name`,`year`,`theme_id`,`num_parts`)');
    }

    private function loadThemeTable($csv)
    {
        return $this->loadCsvFile($csv, 'rebrickable_theme', '(`id`,`name`,@var) SET parent_id = nullif(@var,\'\')');
    }

    private function loadPartTable($csv)
    {
        return $this->loadCsvFile($csv, 'rebrickable_part', '(`id`,`name`,`category_id`)');
    }

    private function loadCategoryTable($csv)
    {
        return $this->loadCsvFile($csv, 'rebrickable_category', '(`id`,`name`)');
    }

    private function loadColorTable($csv)
    {
        return $this->loadCsvFile($csv, 'rebrickable_color', '(`id`,`name`,`rgb`, @var) SET transparent = IF(@var=\'t\',1,0)');
    }
}
