<?php

namespace LoaderBundle\Command;

use League\Flysystem\Exception;
use LoaderBundle\Service\RebrickableLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadRebrickableDataCommand extends ContainerAwareCommand
{
    /** @var RebrickableLoader */
    private $rebrickableLoader;

    /**
     * LoadRebrickableDataCommand constructor.
     *
     * @param string            $name
     * @param RebrickableLoader $rebrickableLoader
     */
    public function __construct($name = null, RebrickableLoader $rebrickableLoader)
    {
        $this->rebrickableLoader = $rebrickableLoader;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('app:load:rebrickable')
            ->setDescription('Loads Rebrickable data about sets and parts into database.')
            ->setHelp('This command allows you to load Rebrickable CSV files containing information about sets and parts into database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->rebrickableLoader->setOutput($output);

        try {
            $this->rebrickableLoader->loadAll();
        } catch (Exception $exception) {
            $output->writeln("<error>{$exception->getMessage()}</error>");

            return 1;
        }

        // Load relations between LDraw models and Rebrickable parts
        $loadRelationsCommand = $this->getApplication()->find('app:load:relations');

        $returnCode = $loadRelationsCommand->run(new ArrayInput(['command' => 'app:load:relations']), $output);

        if ($returnCode) {
            return 1;
        }

        // Populate Index
        $elasticIndex = $this->getApplication()->find('fos:elastic:populate');
        $returnCode = $elasticIndex->run($input, $output);

        if ($returnCode) {
            return 1;
        }

        return 0;
    }
}
