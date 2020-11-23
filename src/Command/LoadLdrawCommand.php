<?php

namespace App\Command;

use App\Service\Loader\LdrawLoader;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadLdrawCommand extends Command
{
    protected static $defaultName = 'app:load:ldraw';

    use LockableTrait;

    private LdrawLoader $ldrawLoader;
    private LoggerInterface $logger;
    private ?string $libraryPath;

    /**
     * LoadLdrawCommand constructor.
     */
    public function __construct(LdrawLoader $modelLoader, string $ldrawLibraryPath, LoggerInterface $logger)
    {
        $this->ldrawLoader = $modelLoader;
        $this->libraryPath = $ldrawLibraryPath;
        $this->logger = $logger;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Loads LDraw library models into database')
            ->setHelp('This command al  lows you to load LDraw library models into database while converting .dat files to .stl format.')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('ldraw', 'l', InputOption::VALUE_OPTIONAL, 'Path to LDraw library directory'),
                    new InputOption('all', 'a', InputOption::VALUE_NONE, 'Load all models from LDraw library folder (/parts directory)'),
                    new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'Load single model into database'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->ldrawLoader->setOutput($io);

        if (!$input->getOption('file') && !$input->getOption('all')) {
            $output->writeln('Either the --all or --file option is required');

            return 1;
        }

        if ($ldraw = $input->getOption('ldraw')) {
            $this->ldrawLoader->setLDrawFilesystem(realpath($ldraw));
        } else {
            $ldrawLibrary = $this->ldrawLoader->downloadLibrary($this->libraryPath);
            $this->ldrawLoader->setLDrawFilesystem($ldrawLibrary);
        }

        if (null !== ($path = $input->getOption('file'))) {
            if ($file = realpath($path)) {
                $io->writeln("Loading model: {$path}");

                $this->ldrawLoader->loadOne($file);

                $errorCount = $this->logger->countErrors();

                if ($errorCount) {
                    $io->warning('Done with "'.$errorCount.'" errors.');
                } else {
                    $io->success('Done with "'.$errorCount.'" errors.');
                }
            } else {
                $io->error("File $path not found");
            }
        }

        // Load all models inside ldraw/parts directory
        if ($input->getOption('all')) {
            $this->ldrawLoader->loadAll();

            $errorCount = $this->logger->countErrors();

            if ($errorCount) {
                $io->warning('Finished with "'.$errorCount.'" errors.');
            } else {
                $io->success('Finished with "'.$errorCount.'" errors.');
            }
        }

        return 0;
    }
}
