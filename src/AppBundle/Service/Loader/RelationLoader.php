<?php

namespace AppBundle\Service\Loader;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Repository\LDraw\ModelRepository;
use AppBundle\Repository\Rebrickable\PartRepository;
use AppBundle\Util\RelationMapper;

class RelationLoader extends BaseLoader
{
    /** @var RelationMapper */
    private $relationMapper;

    /** @var ModelRepository */
    private $modelRepository;

    /** @var PartRepository */
    private $partRepository;

    /**
     * RelationLoader constructor.
     *
     * @param RelationMapper  $relationMapper
     * @param ModelRepository $modelRepository
     * @param PartRepository  $partRepository
     */
    public function __construct($relationMapper, $modelRepository, $partRepository)
    {
        $this->relationMapper = $relationMapper;
        $this->modelRepository = $modelRepository;
        $this->partRepository = $partRepository;
    }

    public function loadAll()
    {
        $parts = $this->partRepository->findAll();
        $this->load($parts);
    }

    public function loadNotPaired()
    {
        $parts = $this->partRepository->findAllNotPaired();
        $this->load($parts);
    }

    private function load($parts)
    {
        $this->initProgressBar(count($parts));
        /** @var Part $part */
        foreach ($parts as $part) {
            $model = $this->loadPartRelation($part);

            if ($model) {
                $part->setModel($model);
                $this->partRepository->save($part);
            }

            $this->progressBar->setMessage($part->getId());
            $this->progressBar->advance();
        }
        $this->progressBar->finish();
    }

    /**
     * Loads relations between Rebrickable part and ldraw models for $parts.
     *
     * @param Part $part
     *
     * @return Model|null
     */
    private function loadPartRelation(Part $part)
    {
        $number = $part->getId();
        $model = $this->modelRepository->findOneByNumber($number);
        if (!$model) {
            $number = $this->relationMapper->find($this->getPrintedParentId($number), 'part_model');
            $model = $this->modelRepository->findOneByNumber($number);

            if (!$model) {
                $model = $this->modelRepository->findOneByName($part->getName());
            }
        }

        return $model;
    }

    /**
     * Get id of parent for printed parts form part id.
     *
     * @param $number
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
