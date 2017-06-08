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
            LoadBaseData::class
        ]);

        $this->modelService = $this->get(ModelService::class);
        $this->setService = $this->get(SetService::class);

        $this->filesystem->write('models/1.stl',file_get_contents(__DIR__.'/../Fixtures/models/1.stl'));

        $this->zipService = new ZipService($this->filesystem,$this->modelService,$this->setService);
    }

    public function tearDown()
    {
        $this->filesystem->delete('models/1.stl');
    }

    public function testModelZip()
    {
        $model = $this->modelService->findModel(1);

        $path = $this->zipService->createFromModel($model, 'modelzip');

        $this->assertFileExists($path);
    }

    public function testSetZip()
    {
        $set = $this->setService->findSet('8049-1');

        $path = $this->zipService->createFromSet($set, 'setzip');

        $this->assertFileExists($path);
    }
}