<?php

namespace Tests\AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Author;
use AppBundle\Repository\LDraw\AuthorRepository;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class AuthorRepositoryTest extends BaseTest
{
    /** @var AuthorRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Author::class);
    }

    public function testGetOrCreate()
    {
        $this->assertCount(1, $this->repository->findAll());

        $author = $this->repository->getOrCreate('Author');
        $this->repository->save($author);
        $this->assertCount(1, $this->repository->findAll());

        $author = $this->repository->getOrCreate('Author2');
        $this->repository->save($author);
        $this->assertCount(2, $this->repository->findAll());
    }
}
