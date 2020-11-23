<?php

namespace App\Repository\LDraw;

use App\Entity\LDraw\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function findOneByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * Get existing entity or create new.
     *
     * @param $name
     *
     * @return Author
     */
    public function getOrCreate($name)
    {
        if ($author = $this->findOneByName($name)) {
            return $author;
        }

        $uow = $this->_em->getUnitOfWork()->getScheduledEntityInsertions();

        foreach ($uow as $scheduled) {
            if ($scheduled instanceof Author) {
                if ($scheduled->getName() == $name) {
                    return $scheduled;
                }
            }
        }

        $author = new Author();
        $author->setName($name);

        return $author;
    }
}
