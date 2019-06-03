<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Liip\FunctionalTestBundle\Test\WebTestCase;

abstract class BaseTest extends WebTestCase
{
    /* @var FilesystemInterface $filesystem */
    protected $filesystem;

    /** @var EntityManagerInterface */
    protected $em;

    public function setUp()
    {
        self::bootKernel();

        $this->filesystem = self::$container->get('oneup_flysystem.media_filesystem');
        $this->em = self::$container->get('doctrine.orm.entity_manager');
    }

    protected function getParameter($parameter)
    {
        return $this->getContainer()->getParameter($parameter);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->filesystem->deleteDir('models');
        $this->filesystem->deleteDir('images');

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}
