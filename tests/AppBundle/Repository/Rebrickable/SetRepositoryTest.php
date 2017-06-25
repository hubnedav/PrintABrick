<?php

namespace Tests\AppBundle\Repository\Rebrickable;


use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Repository\Rebrickable\SetRepository;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class SetRepositoryTest extends BaseTest
{
    /** @var SetRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Set::class);
    }

    public function testFindAllByPart()
    {
        /** @var Part $part */
        $part = $this->em->getRepository(Part::class)->find(1);

        $this->assertCount(1,$this->repository->findAllByPart($part));
    }

    public function testFindAllByModel()
    {
        /** @var Model $model */
        $model = $this->em->getRepository(Model::class)->find(1);

        $this->assertCount(1,$this->repository->findAllByModel($model));
    }

    public function testGetMinPartCount()
    {
        $this->assertEquals(1,$this->repository->getMinPartCount());
    }

    public function testGetMaxPartCount()
    {
        $this->assertEquals(2,$this->repository->getMaxPartCount());
    }

    public function testGetMinYear()
    {
        $this->assertEquals(2011,$this->repository->getMinYear());
    }

    public function testCount()
    {
        $this->assertEquals(2,$this->repository->count());
    }

    public function testGetMaxYear()
    {
        $this->assertEquals(2015,$this->repository->getMaxYear());
    }
}