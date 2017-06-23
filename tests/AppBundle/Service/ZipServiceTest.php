<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\ModelService;
use AppBundle\Service\SetService;
use AppBundle\Service\ZipService;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class ZipServiceTest extends BaseTest
{
    /** @var ZipService */
    private $zipService;

    /** @var ModelService */
    private $modelService;

    /** @var SetService */
    private $setService;

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures([
            LoadBaseData::class,
        ]);

        $this->modelService = new ModelService($this->em);
        $this->setService = new SetService($this->em);

        $this->filesystem->write('models/1.stl', file_get_contents(__DIR__.'/../Fixtures/models/1.stl'));

        $this->zipService = new ZipService($this->filesystem, $this->modelService, $this->setService);
    }

    public function testModelZip()
    {
        $model = $this->modelService->find(1);

        $path = $this->zipService->createFromModel($model, 'modelzip');

        $this->assertFileExists($path);
    }

    public function testSetZip()
    {
        $set = $this->setService->find('8049-1');

        $path = $this->zipService->createFromSet($set, 'setzip');

        $this->assertFileExists($path);
    }
}
