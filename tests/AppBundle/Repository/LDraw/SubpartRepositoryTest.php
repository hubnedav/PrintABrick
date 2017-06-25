<?php

namespace Tests\AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Subpart;
use AppBundle\Repository\LDraw\SubpartRepository;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class SubpartRepositoryTest extends BaseTest
{
    /** @var SubpartRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Subpart::class);
    }

    public function testGetOrCreate()
    {
        $this->assertCount(3, $this->repository->findAll());

        /** @var Model $model */
        $model = $this->em->getRepository(Model::class)->find(1);
        /** @var Model $child */
        $child = $this->em->getRepository(Model::class)->find(2);

        $subpart = $this->repository->getOrCreate($model, $child, 2, 1);
        $this->repository->save($subpart);
        $this->assertCount(3, $this->repository->findAll());

        $subpart = $this->repository->getOrCreate($model, $child, 2, 2);
        $this->repository->save($subpart);
        $this->assertCount(4, $this->repository->findAll());

        $subpart = $this->repository->getOrCreate($model, $child, 2, 3);
        $this->repository->save($subpart);
        $this->assertCount(5, $this->repository->findAll());
    }
}
