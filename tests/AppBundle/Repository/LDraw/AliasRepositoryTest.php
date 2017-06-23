<?php

namespace Tests\AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Repository\LDraw\AliasRepository;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class AliasRepositoryTest extends BaseTest
{
    /** @var AliasRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Alias::class);
    }

    public function testGetOrCreate()
    {
        $this->assertCount(2, $this->repository->findAll());

        /** @var Model $model */
        $model = $this->em->getRepository(Model::class)->find(1);

        $alias = $this->repository->getOrCreate(25, $model);
        $this->repository->save($alias);
        $this->assertCount(2, $this->repository->findAll());

        $alias = $this->repository->getOrCreate(33, $model);
        $this->repository->save($alias);
        $this->assertCount(3, $this->repository->findAll());
    }
}
