<?php

namespace AppBundle\Service\Loader;

use AppBundle\Api\Manager\RebrickableManager;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Manager\LDraw\ModelManager;
use AppBundle\Repository\Rebrickable\PartRepository;

class RelationLoader extends BaseLoaderService
{
    /** @var ModelManager */
    private $modelManager;

    /** @var PartRepository */
    private $partRepository;

    /** @var RebrickableManager */
    private $rebrickableAPIManager;

    /**
     * RelationLoader constructor.
     *
     * @param $ldrawPartManager
     * @param $rebrickablePartRepository
     */
    public function __construct($modelManager, $partRepository, $rebrickableApiManager)
    {
        $this->modelManager = $modelManager;
        $this->partRepository = $partRepository;
        $this->rebrickableAPIManager = $rebrickableApiManager;
    }


    /**
     *
     */
    public function loadAll()
    {
        $parts = $this->partRepository->findAll();

        $this->initProgressBar(count($parts));
        /** @var Part $part */
        foreach ($parts as $part) {
            $this->load($part);

            $this->progressBar->advance();
        }
        $this->progressBar->finish();
    }

    /**
     *
     */
    public function loadNotPaired()
    {
        $parts = $this->partRepository->findAllNotPaired();

        $this->initProgressBar(count($parts));
        /** @var Part $part */
        foreach ($parts as $part) {
            $this->load($part);

            $this->progressBar->advance();
        }
        $this->progressBar->finish();
    }

    /**
     * Loads relations between Rebrickable part and ldraw models for $parts
     *
     * @param Part $part
     *
     * @return Model $m
     */
    private function load($part)
    {
        $number = $part->getNumber();
        $model = $this->modelManager->findByNumber($number);
        if (!$model) {
            $number = $this->relationMapper->find($this->getPrintedParentId($number), 'part_model');
            $model = $this->modelManager->findByNumber($number);

            if (!$model) {
                $model = $this->modelManager->findByName($part->getName());
            }
        }

        if ($model) {
            $part->setModel($model);
            $this->partRepository->save($part);
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
