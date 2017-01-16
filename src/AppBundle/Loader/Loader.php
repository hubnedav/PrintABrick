<?php

namespace AppBundle\Loader;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class Loader
{
    /**
     * @var EntityManager
     */
    protected $em;

    protected $output;

    public function setOutput(OutputInterface $output) {
        $this->output = $output;
        $this->output->setDecorated(true);
    }
}