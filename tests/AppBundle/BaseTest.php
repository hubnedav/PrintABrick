<?php

namespace Tests\AppBundle;

use AppBundle\DataFixtures\ORM\LoadColors;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use League\Flysystem\FilesystemInterface;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class BaseTest extends WebTestCase
{
    protected $_container;

    /* @var FilesystemInterface $filesystem */
    protected $filesystem;

    /** @var EntityManagerInterface */
    protected $em;

    public function __construct()
    {
        self::bootKernel();
        $this->_container = self::$kernel->getContainer();
        $this->filesystem = $this->get('oneup_flysystem.media_filesystem');
        $this->em = $this->get('doctrine.orm.entity_manager');
        parent::__construct();
    }

    public function setUpDb()
    {
        // Make sure we are in the test environment
        if ('test' !== self::$kernel->getEnvironment()) {
            throw new \LogicException('setUpDb must be executed in the test environment');
        }

        // If you are using the Doctrine Fixtures Bundle you could load these here
        $this->loadFixtures([
            LoadColors::class
        ]);
    }

    protected function get($service)
    {
        return $this->_container->get($service);
    }

    protected function getParameter($parameter)
    {
        return $this->_container->getParameter($parameter);
    }
}
