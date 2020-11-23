<?php

namespace App\Repository\LDraw;

use App\Entity\LDraw\Alias;
use App\Entity\LDraw\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Alias|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alias|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alias[]    findAll()
 * @method Alias[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AliasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alias::class);
    }

    /**
     * Get existing entity or create new.
     */
    public function getOrCreate(Model $parent, Model $child): Alias
    {
        if ($alias = $this->findOneBy(['parent' => $parent, 'child' => $child])) {
            return $alias;
        }

        $uow = $this->_em->getUnitOfWork()->getScheduledEntityInsertions();
        foreach ($uow as $scheduled) {
            if ($scheduled instanceof Alias && $scheduled->getParent() === $parent && $scheduled->getChild() === $child) {
                return $scheduled;
            }
        }

        $alias = new Alias();
        $alias->setParent($parent);
        $alias->setChild($child);

        return $alias;
    }
}
