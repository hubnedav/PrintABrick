<?php

namespace Tests\AppBundle\Service;

use Doctrine\ORM\Tools\SchemaTool;
use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class BaseTest extends KernelTestCase
{
    protected $_container;

    /* @var FilesystemInterface $filesystem */
    protected $filesystem;

    public function __construct()
    {
        self::bootKernel();
        $this->_container = self::$kernel->getContainer();
        parent::__construct();

        $this->filesystem = $this->get('oneup_flysystem.myfilesystem_filesystem');
    }

    public function prime(KernelInterface $kernel)
    {
        // Make sure we are in the test environment
        if ('test' !== $kernel->getEnvironment()) {
            throw new \LogicException('Primer must be executed in the test environment');
        }

        // Get the entity manager from the service container
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Run the schema update tool using our entity metadata
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadatas);

        // If you are using the Doctrine Fixtures Bundle you could load these here
    }

    protected function get($service)
    {
        return $this->_container->get($service);
    }
}
