<?php

namespace AppBundle\Command;

use AppBundle\Service\Loader\ModelLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadModelsCommand extends ContainerAwareCommand
{
    use LockableTrait;

    protected function configure()
    {
        $this
            ->setName('app:load:models')
            ->setDescription('Loads LDraw library models into database')
            ->setHelp('This command allows you to load LDraw library models into database while converting .dat files to .stl format.')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('ldraw', 'l', InputOption::VALUE_OPTIONAL, 'Path to LDraw library directory'),
                    new InputOption('all', 'a', InputOption::VALUE_NONE, 'Load all models from LDraw libary folder (/parts directory)'),
                    new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'Load single modle into database'),
                    new InputOption('update', 'u', InputOption::VALUE_NONE, 'Update models'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 1;
        }

        /** @var ModelLoader $modelLoader */
        $modelLoader = $this->getContainer()->get('service.loader.model');
        $modelLoader->setOutput($output);
        $modelLoader->setRewrite($input->getOption('update'));

        if (!$input->getOption('file') && !$input->getOption('all')) {
            $output->writeln('Either the --all or --file option is required');

            return 1;
        }

        if ($ldraw = $input->getOption('ldraw')) {
            $modelLoader->setLDrawLibraryContext(realpath($ldraw));
        } else {
            $ldraw = $modelLoader->downloadLibrary($this->getContainer()->getParameter('app.ld_library_download_url'));
            $modelLoader->setLDrawLibraryContext($ldraw);
        }

        if (($path = $input->getOption('file')) != null) {
            if ($file = realpath($path)) {
                $output->writeln([
                    "Loading model: {$path}",
                ]);

                $modelLoader->loadOne($file);

                $errorCount = $this->getContainer()->get('monolog.logger.loader')->countErrors();
                $errors = $errorCount ? '<error>'.$errorCount.'</error>' : '<info>0</info>';

                $output->writeln(['Done with "'.$errors.'" errors.']);
            } else {
                $output->writeln("File $path not found");
            }
        }

        // Load all models inside ldraw/parts directory
        if ($input->getOption('all')) {
            $modelLoader->loadAll();

            $errorCount = $this->getContainer()->get('monolog.logger.loader')->countErrors();
            $errors = $errorCount ? '<error>'.$errorCount.'</error>' : '<info>0</info>';

            $output->writeln(['Done with "'.$errors.'" errors.']);
        }

        $this->release();
    }
}
