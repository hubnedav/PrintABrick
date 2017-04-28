<?php

namespace AppBundle\Service\Loader;

use AppBundle\Api\Manager\RebrickableManager;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Utils\RelationMapper;

class RelationLoader extends BaseLoader
{
    /** @var RelationMapper */
    private $relationMapper;

    /** @var RebrickableManager */
    private $rebrickableAPIManager;

    /**
     * RelationLoader constructor.
     *
     * @param RebrickableManager $rebrickableApiManager
     * @param RelationMapper     $relationMapper
     */
    public function __construct($rebrickableApiManager, $relationMapper)
    {
        $this->rebrickableAPIManager = $rebrickableApiManager;
        $this->relationMapper = $relationMapper;
    }

    public function loadAll()
    {
        $parts = $this->em->getRepository(Part::class)->findAll();
        $this->load($parts);
    }

    public function loadNotPaired($parts)
    {
        $parts = $this->em->getRepository(Part::class)->findAllNotPaired();
        $this->load($parts);
    }

    private function load($parts)
    {
        $this->initProgressBar(count($parts));
        /** @var Part $part */
        foreach ($parts as $part) {
            $this->loadPartRelation($part);
            $this->progressBar->setMessage($part->getNumber());
            $this->progressBar->advance();
        }
        $this->progressBar->finish();
    }

    /**
     * Loads relations between Rebrickable part and ldraw models for $parts.
     *
     * @param Part $part
     *
     * @return Model $m
     */
    private function loadPartRelation(Part $part)
    {
        $modelRepository = $this->em->getRepository(Model::class);

        $number = $part->getNumber();
        $model = $modelRepository->findOneByNumber($number);
        if (!$model) {
            $number = $this->relationMapper->find($this->getPrintedParentId($number), 'part_model');
            $model = $modelRepository->findOneByNumber($number);

            if (!$model) {
                $model = $modelRepository->findOneByName($part->getName());
            }
        }

        if ($model) {
            $part->setModel($model);
            $this->em->getRepository(Part::class)->save($part);
        }
    }

    /**
     * Get printed part parent number.
     *
     * @param $id
     *
     * @return string|null LDraw number of printed part parent
     */
    private function getPrintedParentId($number)
    {
        if (preg_match('/(^970[c,x])([0-9a-z]*)$/', $number, $matches)) {
            return '970c00';
        } elseif (preg_match('/(^973)([c,p][0-9a-z]*)$/', $number, $matches)) {
            return '973c00';
        } elseif (preg_match('/(^.*)((pr[x]{0,1}[0-9]{1,7}[a-z]{0,1})|(pat[[0-9]{1,4}[a-z]{0,1}))$/', $number, $matches)) {
            return $matches[1];
        } elseif (preg_match('/(^.*)((pb[0-9]{1,4}[a-z]{0,1}))$/', $number, $matches)) {
            return $matches[1];
        } elseif (preg_match('/(^.*)(p[x]{0,1}[0-9a-z]{2,4})$/', $number, $matches)) {
            return $matches[1];
        }

        return $number;
    }
}
