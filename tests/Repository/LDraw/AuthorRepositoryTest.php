<?php

namespace App\Tests\Repository\LDraw;

use App\Entity\LDraw\Author;
use App\Repository\LDraw\AuthorRepository;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class AuthorRepositoryTest extends BaseTest
{
    /** @var AuthorRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);

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
