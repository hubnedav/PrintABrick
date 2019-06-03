<?php

namespace App\Tests\Service;

use App\Entity\LDraw\Model;
use App\Service\ModelService;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class ModelServiceTest extends BaseTest
{
    /** @var ModelService */
    private $modelService;

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures([
            LoadBaseData::class,
        ]);

        $this->modelService = new ModelService($this->em);
    }

    public function testGetSiblings()
    {
        $model = $this->em->getRepository(Model::class)->find(1);
        $this->assertCount(0, $this->modelService->getSiblings($model));

        $model = $this->em->getRepository(Model::class)->find(2);
        $this->assertCount(1, $this->modelService->getSiblings($model));
    }

    public function testGetSubmodels()
    {
        $model = $this->em->getRepository(Model::class)->find(1);
        $this->assertCount(2, $this->modelService->getSubmodels($model));
    }

    public function testGetTotalCount()
    {
        $this->assertEquals(4, $this->modelService->getTotalCount());
    }
}
