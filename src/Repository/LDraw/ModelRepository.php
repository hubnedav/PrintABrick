<?php

namespace App\Repository\LDraw;

use App\Entity\LDraw\Alias;
use App\Entity\LDraw\Model;
use App\Entity\LDraw\ModelType;
use App\Entity\LDraw\Subpart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Model|null find($id, $lockMode = null, $lockVersion = null)
 * @method Model|null findOneBy(array $criteria, array $orderBy = null)
 * @method Model[]    findAll()
 * @method Model[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    public function findOneByPartOrModelNumber($number)
    {
        if ($model = $this->findOnePartByNumber($number)) {
            return $model;
        }

        return $this->createQueryBuilder('model')
            ->leftJoin('model.parts', 'part')
            ->where('part.id LIKE :number')
            ->setParameter('number', $number)
            ->getQuery()->getOneOrNullResult();
    }

    public function findOnePartByNumber($number)
    {
        $qb = $this->createQueryBuilder('model');

        $qb->leftJoin('model.parents', 'r')
            ->leftJoin('r.parent', 'p')
            ->leftJoin('model.type', 't')
            ->where($qb->expr()->andX(
                $qb->expr()->like('p.id', $qb->expr()->literal($number)),
                $qb->expr()->isInstanceOf('r', Alias::class)
            ))
            ->orWhere($qb->expr()->andX(
                $qb->expr()->like('model.id', $qb->expr()->literal($number)),
                $qb->expr()->in('t.name', [
                    ModelType::PART,
                    ModelType::SHORTCUT,
                ])
            ))
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneByNumber($number)
    {
        $qb = $this->createQueryBuilder('model');

        $qb->leftJoin('model.parents', 'r')
            ->leftJoin('r.parent', 'p')
            ->leftJoin('model.type', 't')
            ->where($qb->expr()->andX(
                $qb->expr()->like('p.id', $qb->expr()->literal($number)),
                $qb->expr()->isInstanceOf('r', Alias::class)
            ))
            ->orWhere($qb->expr()->andX(
                $qb->expr()->like('model.id', $qb->expr()->literal($number)),
                $qb->expr()->in('t.name', [
                    ModelType::PART,
                    ModelType::SHORTCUT,
                    ModelType::ALIAS,
                    ModelType::SHORTCUT_COLOUR,
                    ModelType::PRINTED,
                    ModelType::PART_COLOUR,
                    ModelType::PART_FLEXIBLE,
                ])
            ))
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findAllSiblings(Model $model)
    {
        $qb = $this->createQueryBuilder('model');

        $qb->select('related')
            ->join(Subpart::class, 'subpart', JOIN::WITH, 'model.id = subpart.child')
            ->join(Subpart::class, 'parent', JOIN::WITH, 'subpart.parent = parent.parent')
            ->join(Model::class, 'related', JOIN::WITH, 'related.id = parent.child')
            ->leftJoin('related.type', 't')
            ->where('model.id = :number')
            ->setParameter('number', $model->getId())
            ->andWhere('related.id != :number')
            ->andWhere($qb->expr()->in('t.name', [
                ModelType::PART,
                ModelType::SHORTCUT,
                ModelType::ALIAS,
                ModelType::PART_COLOUR,
                ModelType::PART_FLEXIBLE,
                ModelType::PRINTED,
                ModelType::SHORTCUT_COLOUR,
            ]))
            ->distinct(true);

        return $qb->getQuery()->getResult();
    }

    public function findAllParents(Model $model)
    {
        $qb = $this->createQueryBuilder('model');

        $qb->select('related')
            ->join(Subpart::class, 'p', JOIN::WITH, 'model.id = p.child')
            ->join(Subpart::class, 's', JOIN::WITH, 's.parent = p.parent')
            ->join(Model::class, 'related', JOIN::WITH, 'related.id = p.child')
            ->where('model.id = :number')
            ->setParameter('number', $model->getId())
            ->andWhere('related.id != :number')
            ->andWhere($qb->expr()->in('t.name', [
                ModelType::PART,
                ModelType::SHORTCUT,
                ModelType::ALIAS,
                ModelType::PART_COLOUR,
                ModelType::PART_FLEXIBLE,
                ModelType::PRINTED,
                ModelType::SHORTCUT_COLOUR,
            ]))
            ->distinct(true);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find models scheduled for insertion of update by id.
     *
     * @param $id
     *
     * @return ?Model
     */
    public function findScheduled($id): ?Model
    {
        $uow = $this->_em->getUnitOfWork()->getScheduledEntityInsertions();

        foreach ($uow as $scheduled) {
            if ($scheduled instanceof Model && $scheduled->getId() === $id) {
                return $scheduled;
            }
        }

        $uow = $this->_em->getUnitOfWork()->getScheduledEntityUpdates();

        foreach ($uow as $scheduled) {
            if ($scheduled instanceof Model && $scheduled->getId() === $id) {
                return $scheduled;
            }
        }

        return null;
    }

    /**
     * Create new Model entity with $number or retrieve one.
     *
     * @param $id
     */
    public function getOrCreate($id): Model
    {
        if ($model = $this->find($id)) {
            return $model;
        }

        if ($model = $this->findScheduled($id)) {
            return $model;
        }

        $model = new Model();
        $model->setId($id);

        return $model;
    }
}
