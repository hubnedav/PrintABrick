<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Subpart;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class ModelRepository extends BaseRepository
{
    /**
     * Find model by id or alias id.
     *
     * @param $number
     *
     * @return mixed
     */
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

    public function count()
    {
        $queryBuilder = $this->createQueryBuilder('model');
        $queryBuilder->select('count(model)');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Create new Model entity with $number or retrieve one.
     *
     * @param $id
     *
     * @return Model
     */
    public function getOrCreate($id)
    {
        if (($model = $this->findOneByNumber($id)) == null) {
            $model = new Model();
            $model->setId($id);
        }

        return $model;
    }
}
