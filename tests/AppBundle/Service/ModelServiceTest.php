<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Service\ModelService;
use AppBundle\Service\SetService;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

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

        $this->assertCount(0,$this->modelService->getSiblings($model));

        $model = $this->em->getRepository(Model::class)->find(2);

        $this->assertCount(1,$this->modelService->getSiblings($model));
    }

    public function testGetSubmodels()
    {
        $model = $this->em->getRepository(Model::class)->find(1);

        $this->assertCount(2,$this->modelService->getSubmodels($model));
    }

    public function testGetTotalCount()
    {
        $this->assertEquals(4,$this->modelService->getTotalCount());
    }
}