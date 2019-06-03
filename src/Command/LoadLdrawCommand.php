<?php

namespace App\Command;

use App\Service\Loader\ModelLoader;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadLdrawCommand extends Command
{
    use LockableTrait;

    /** @var ModelLoader */
    private $modelLoader;

    /** @var LoggerInterface */
    private $logger;

    private $libraryPath;

    /**
     * LoadLdrawCommand constructor.
     *
     * @param ModelLoader     $modelLoader
     * @param string          $ldrawLibraryPath
     * @param LoggerInterface $logger
     * @param string          $name
     */
    public function __construct(ModelLoader $modelLoader, $ldrawLibraryPath, LoggerInterface $logger, string $name = null)
    {
        $this->modelLoader = $modelLoader;
        $this->libraryPath = $ldrawLibraryPath;
        $this->logger = $logger;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('app:load:ldraw')
            ->setDescription('Loads LDraw library models into database')
            ->setHelp('This command allows you to load LDraw library models into database while converting .dat files to .stl format.')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('ldraw', 'l', InputOption::VALUE_OPTIONAL, 'Path to LDraw library directory'),
                    new InputOption('all', 'a', InputOption::VALUE_NONE, 'Load all models from LDraw library folder (/parts directory)'),
                    new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'Load single model into database'),
                    new InputOption('update', 'u', InputOption::VALUE_NONE, 'Update models'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->modelLoader->setOutput($output);
        $this->modelLoader->setRewrite($input->getOption('update'));

        if (!$input->getOption('file') && !$input->getOption('all')) {
            $output->writeln('Either the --all or --file option is required');

            return 1;
        }

        if ($ldraw = $input->getOption('ldraw')) {
            $this->modelLoader->setLDrawLibraryContext(realpath($ldraw));
        } else {
            $ldraw = $this->modelLoader->downloadLibrary($this->libraryPath);
            $this->modelLoader->setLDrawLibraryContext($ldraw);
        }

        if (null !== ($path = $input->getOption('file'))) {
            if ($file = realpath($path)) {
                $output->writeln([
                    "Loading model: {$path}",
                ]);

                $this->modelLoader->loadOne($file);

                $errorCount = $this->logger->countErrors();
                $errors = $errorCount ? '<error>'.$errorCount.'</error>' : '<info>0</info>';

                $output->writeln(['Done with "'.$errors.'" errors.']);
            } else {
                $output->writeln("File $path not found");
            }
        }

        // Load all models inside ldraw/parts directory
        if ($input->getOption('all')) {
            $this->modelLoader->loadAll();

            $errorCount = $this->logger->countErrors();
            $errors = $errorCount ? '<error>'.$errorCount.'</error>' : '<info>0</info>';

            $output->writeln(['Done with "'.$errors.'" errors.']);
        }

        return 0;
    }
}
