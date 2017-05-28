<?php

namespace Tests\AppBundle\Service;

use AppBundle\DataFixtures\ORM\LoadColors;
use Doctrine\ORM\EntityManager;
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

    public function __construct()
    {
        self::bootKernel();
        $this->_container = self::$kernel->getContainer();
        parent::__construct();

        $this->filesystem = $this->get('oneup_flysystem.media_filesystem');
    }

    public function setUpDb()
    {
        // Make sure we are in the test environment
        if ('test' !== self::$kernel->getEnvironment()) {
            throw new \LogicException('Primer must be executed in the test environment');
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
}
