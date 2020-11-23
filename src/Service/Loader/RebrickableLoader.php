<?php

namespace App\Service\Loader;

use App\Entity\Rebrickable\Part;
use App\Exception\Loader\LoadingRebrickableFailedException;
use Doctrine\ORM\EntityManagerInterface;

//TODO Refactor + validate csv files
class RebrickableLoader extends LoggerAwareLoader
{
    private EntityManagerInterface $em;
    private $downloadsPage;
    private $csvFile;

    /**
     * RebrickableLoader constructor.
     *
     * @param $rebrickableDownloadsPage
     */
    public function __construct(EntityManagerInterface $em, $rebrickableDownloadsPage)
    {
        parent::__construct();
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger();
        $this->downloadsPage = $rebrickableDownloadsPage;
    }

    /**
     * Truncates and loads all rebrickable tables from csv files.
     *
     * @throws LoadingRebrickableFailedException
     */
    public function loadAll($truncate = false)
    {
        $connection = $this->em->getConnection();
        $connection->beginTransaction();

        try {
            $this->loadCsvFiles();

            if ($truncate) {
                $connection->prepare('SET foreign_key_checks = 0;')->execute();
                $this->truncateTables();
                $connection->prepare('SET foreign_key_checks = 1;')->execute();
            }

            $this->output->writeln([
                '<info>Truncated</info> <comment>rebrickable</comment> <info>database tables.</info>',
                'Loading CSV files into database...',
            ]);

            $connection->prepare('SET foreign_key_checks = 0;')->execute();
            $this->output->progressStart(10);

            $this->loadColorTable($this->csvFile['colors']);
            $this->output->progressAdvance();

            $this->loadCategoryTable($this->csvFile['part_categories']);
            $this->output->progressAdvance();

            $this->loadPartTable($this->csvFile['parts']);
            $this->output->progressAdvance();

            $this->loadElementTable($this->csvFile['elements']);
            $this->output->progressAdvance();

            $this->loadThemeTable($this->csvFile['themes']);
            $this->output->progressAdvance();

            $this->loadSetTable($this->csvFile['sets']);
            $this->output->progressAdvance();

            $this->loadInventoryTable($this->csvFile['inventories']);
            $this->output->progressAdvance();

            $this->loadInventorySetTable($this->csvFile['inventory_sets']);
            $this->output->progressAdvance();

            $this->loadPartRelationshipssTable($this->csvFile['part_relationships']);
            $this->output->progressAdvance();

            $this->loadInventoryPartTable($this->csvFile['inventory_parts']);
            $this->output->progressAdvance();

            $connection->prepare('SET foreign_key_checks = 1;')->execute();
            $this->output->progressFinish();

            $this->addMissingParts();

            $connection->commit();

            $this->output->writeln(['Rebrickable database loaded successfully!']);
        } catch (\Exception $e) {
//            $connection->rollBack();

            throw new LoadingRebrickableFailedException($e->getMessage());
        }
    }

    /**
     * Downloads csv files from rebrickable_url specified in config.yml.
     */
    private function loadCsvFiles()
    {
        // Load html content of Rebrickable downloads page
        $downloadsPageContent = file_get_contents($this->downloadsPage);
        // Find all download csv links on page
        preg_match_all('/"((.*)\/(.*).csv.gz\?(.*))"/', $downloadsPageContent, $currentLinks);

        $csvLinks = array_combine($currentLinks[3], $currentLinks[1]);

        $csvToDownload = [
            'inventories',
            'inventory_parts',
            'inventory_sets',
            'sets',
            'elements',
            'themes',
            'parts',
            'colors',
            'part_categories',
            'part_relationships',
        ];

        $this->output->writeln([
            '<fg=cyan>------------------------------------------------------------------------------</>',
            '<fg=cyan>Loading Rebrickable CSV files</>',
            '<fg=cyan>------------------------------------------------------------------------------</>',
        ]);

        foreach ($csvToDownload as $item) {
            $this->csvFile[$item] = $this->downloadGzFile($csvLinks[$item]);
        }

        $this->output->writeln(['<info>All CSV files loaded.</info>']);
    }

    /**
     * Truncate content of rebrickable tables.
     *
     * @return bool
     */
    private function truncateTables()
    {
        $query = '
            TRUNCATE rebrickable_inventory_parts;
            TRUNCATE rebrickable_inventory_sets;
            TRUNCATE rebrickable_inventory;
            TRUNCATE rebrickable_element;
            TRUNCATE rebrickable_set;
            TRUNCATE rebrickable_theme;
            TRUNCATE rebrickable_part;
            TRUNCATE rebrickable_category;
            TRUNCATE rebrickable_part_relationships;
           ';

        return $this->em->getConnection()->prepare($query)->execute();
    }

    /**
     * @param $file
     * @param $table
     * @param $columns
     *
     * @return bool
     */
    private function loadCsvFile($file, $table, $columns)
    {
        $query = sprintf("LOAD DATA LOCAL INFILE '%s' 
            REPLACE INTO TABLE %s
            CHARACTER SET UTF8
            FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
            LINES TERMINATED BY '\\n'
            IGNORE 1 LINES %s", addslashes($file), $table, $columns);

        return $this->em->getConnection()->prepare($query)->execute();
    }

    /**
     * Creates missing Part entites for foreign keys form inventory_parts table.
     */
    private function addMissingParts()
    {
        $connection = $this->em->getConnection();
        $statement = $connection->prepare('SELECT DISTINCT ip.part_id FROM rebrickable_inventory_parts ip LEFT JOIN rebrickable_part p ON ip.part_id = p.id WHERE p.id IS NULL');
        $statement->execute();
        $foreignKeys = $statement->fetchAll();

        foreach ($foreignKeys as $foreignKey) {
            $part = new Part();
            $part->setId($foreignKey['part_id']);
            $this->em->persist($part);
        }
        $this->em->flush();
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
        return $this->loadCsvFile($csv, 'rebrickable_part', '(`id`,`name`,`category_id`, `material`)');
    }

    private function loadElementTable($csv)
    {
        return $this->loadCsvFile($csv, 'rebrickable_element', '(`id`,`part_id`,`color_id`)');
    }

    private function loadCategoryTable($csv)
    {
        return $this->loadCsvFile($csv, 'rebrickable_category', '(`id`,`name`)');
    }

    private function loadPartRelationshipssTable($csv)
    {
        return $this->loadCsvFile($csv, 'rebrickable_part_relationships', '(`type`, `children_id`, `parent_id`)');
    }

    private function loadColorTable($csv)
    {
        return $this->loadCsvFile($csv, 'color', '(`id`,`name`,`rgb`, @var) SET transparent = IF(@var=\'t\',1,0)');
    }
}
