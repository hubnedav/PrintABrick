<?php

namespace App\Command;

use App\Service\Loader\ImageLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadImagesCommand extends Command
{
    protected static $defaultName = 'app:load:images';

    private ImageLoader $imageLoader;

    /**
     * LoadImagesCommand constructor.
     */
    public function __construct(ImageLoader $imageLoader)
    {
        $this->imageLoader = $imageLoader;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Loads images of models')
            ->setHelp('This command allows you to load rendered images of models from Rebrickable or/and generate rendered images from stl files of models.')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('color', 'c', InputOption::VALUE_REQUIRED, 'Color ID of images to load.'),
                    new InputOption('rebrickable', 'r', InputOption::VALUE_NONE, 'Download images from Rebicable.com'),
                    new InputOption('missing', 'm', InputOption::VALUE_NONE, 'Load missing images of models'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->imageLoader->setOutput($io);

        $color = $input->getOption('color');

        if (null !== $color && $input->getOption('rebrickable')) {
            $this->imageLoader->loadColorFromRebrickable($color);
        }

        if ($input->getOption('missing')) {
            $this->imageLoader->loadMissingModelImages();
        }

        return 0;
    }
}
