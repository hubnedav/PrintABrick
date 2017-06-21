<?php

namespace LoaderBundle\Command;

use LoaderBundle\Service\ImageLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadImagesCommand extends ContainerAwareCommand
{
    private $imageLoader;

    /**
     * LoadImagesCommand constructor.
     */
    public function __construct($name = null, ImageLoader $imageLoader)
    {
        $this->imageLoader = $imageLoader;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('app:load:images')
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
        $this->imageLoader->setOutput($output);

        $color = $input->getOption('color');

        if ($color !== null && $input->getOption('rebrickable')) {
            $this->imageLoader->loadColorFromRebrickable($color);
        }

        if ($input->getOption('missing')) {
            $this->imageLoader->loadMissingModelImages();
        }
    }
}
