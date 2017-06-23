<?php

namespace Tests\AppBundle;

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
        $this->filesystem = $this->get('oneup_flysystem.media_filesystem');
        $this->em = $this->get('doctrine.orm.entity_manager');
    }

    public function setUpDb(array $fixtures)
    {
        // Make sure we are in the test environment
        if ('test' !== $this->get('kernel')->getEnvironment()) {
            throw new \LogicException('setUpDb must be executed in the test environment');
        }

        // If you are using the Doctrine Fixtures Bundle you could load these here
        $this->loadFixtures($fixtures);
    }

    protected function get($service)
    {
        return $this->getContainer()->get($service);
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
