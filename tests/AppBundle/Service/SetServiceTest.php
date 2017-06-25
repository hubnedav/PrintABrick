<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Service\SetService;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class SetServiceTest extends BaseTest
{
    /** @var SetService */
    private $setService;

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures([
            LoadBaseData::class,
        ]);

        $this->setService = new SetService($this->em);
    }

    public function testGetModels() {
        /** @var Set $set */
        $set = $this->em->getRepository(Set::class)->find('8049-1');
        $models = $this->setService->getModels($set);

        $this->assertCount(2, $models);
    }

    public function testGetTotalCount() {
        $this->assertEquals(2   ,$this->setService->getTotalCount());
    }

    public function testGetPartCount() {
        /** @var Set $set */
        $set = $this->em->getRepository(Set::class)->find('8049-1');
        $this->assertEquals(14,$this->setService->getPartCount($set));
    }

    public function testGetAllByModel() {
        $model = $this->em->getRepository(Model::class)->find(1);
        $this->assertCount(1,$this->setService->getAllByModel($model));
    }

    public function testGetAllByPart() {
        $part = $this->em->getRepository(Part::class)->find(1);
        $this->assertCount(1,$this->setService->getAllByPart($part));
    }

    public function testGetModelsGroupedByColor() {
        /** @var Set $set */
        $set = $this->em->getRepository(Set::class)->find('8049-1');

        $this->assertCount(2,$this->setService->getModelsGroupedByColor($set));
    }

    public function testGetParts() {
        /** @var Set $set */
        $set = $this->em->getRepository(Set::class)->find('8049-1');

        $this->assertCount(2,$this->setService->getModelsGroupedByColor($set));
    }

    public function testGetAllSubsets() {
        /** @var Set $set */
        $set = $this->em->getRepository(Set::class)->find('8049-1');

        $this->assertCount(1,$this->setService->getAllSubSets($set));
    }
}