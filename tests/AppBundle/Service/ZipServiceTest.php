<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Service\ModelService;
use AppBundle\Service\SetService;
use AppBundle\Service\ZipService;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class ZipServiceTest extends BaseTest
{
    /** @var ZipService */
    private $zipService;

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures([
            LoadBaseData::class,
        ]);

        $this->filesystem->write('models/1.stl', file_get_contents(__DIR__.'/../Fixtures/models/1.stl'));

        $this->zipService = new ZipService($this->filesystem, new ModelService($this->em), new SetService($this->em));
    }

    public function testModelZip()
    {
        $model = $this->em->getRepository(Model::class)->find(1);

        $path = $this->zipService->createFromModel($model, 'modelzip');

        $this->assertFileExists($path);
    }

    public function testSetZip()
    {
        $set = $this->em->getRepository(Set::class)->find('8049-1');

        $path = $this->zipService->createFromSet($set, 'setzip');

        $this->assertFileExists($path);
    }

    public function testSetGroupedByColorZip()
    {
        $set = $this->em->getRepository(Set::class)->find('8049-1');

        $path = $this->zipService->createFromSet($set, 'setzip', true);

        $this->assertFileExists($path);
    }
}
