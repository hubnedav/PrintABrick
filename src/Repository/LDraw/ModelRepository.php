<?php

namespace App\Repository\LDraw;

use App\Entity\LDraw\Alias;
use App\Entity\LDraw\Model;
use App\Entity\LDraw\Subpart;
use App\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class ModelRepository extends BaseRepository
{
    public function findOneByNumber($number)
    {
        $model = $this->createQueryBuilder('model')
            ->where('model.id LIKE :number')
            ->setParameter('number', $number)
            ->getQuery()->getOneOrNullResult();

        if (!$model) {
            $model = $this->createQueryBuilder('model')
                ->leftJoin(Alias::class, 'alias', JOIN::WITH, 'alias.model = model')
                ->where('alias.id LIKE :number')
                ->setParameter('number', $number)
                ->getQuery()->getOneOrNullResult();
        }

        return $model;
    }

    public function findOneByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findAllSiblings(Model $model)
    {
        $queryBuilder = $this->createQueryBuilder('model');

        $queryBuilder
            ->select('related')
            ->join(Subpart::class, 'subpart', JOIN::WITH, 'model.id = subpart.subpart')
            ->join(Subpart::class, 'parent', JOIN::WITH, 'subpart.parent = parent.parent')
            ->join(Model::class, 'related', JOIN::WITH, 'related.id = parent.subpart')
            ->where('model.id = :number')
            ->setParameter('number', $model->getId())
            ->andWhere('related.id != :number')
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Create new Model entity with $number or retrieve one.
     *
     * @param $id
     *
     * @return Model
     */
    public function getOrCreate($id): Model
    {
        if (null === ($model = $this->findOneByNumber($id))) {
            $model = new Model();
            $model->setId($id);
        }

        return $model;
    }
}
