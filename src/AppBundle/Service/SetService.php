<?php

namespace AppBundle\Service;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Inventory_Set;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Repository\Rebrickable\Inventory_PartRepository;
use AppBundle\Repository\Rebrickable\Inventory_SetRepository;
use AppBundle\Repository\Rebrickable\SetRepository;
use Doctrine\ORM\EntityManagerInterface;

class SetService
{
    /** @var SetRepository */
    private $setRepository;

    /** @var Inventory_PartRepository */
    private $inventoryPartRepository;

    /** @var Inventory_SetRepository */
    private $inventorySetRepository;

    /**
     * SetService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->setRepository = $em->getRepository(Set::class);
        $this->inventoryPartRepository = $em->getRepository(Inventory_Part::class);
        $this->inventorySetRepository = $em->getRepository(Inventory_Set::class);
    }

    /**
     * Find set by id.
     *
     * @param $id
     *
     * @return object
     */
    public function find($id)
    {
        return $this->setRepository->find($id);
    }

    /**
     * Get all sets in the set inventory.
     *
     * @param Set $set
     *
     * @return array|null
     */
    public function getAllSubSets(Set $set)
    {
        return $this->inventorySetRepository->findAllBySetNumber($set->getId());
    }

    /**
     * Get all sets in which the model appears.
     *
     * @param Model $model
     *
     * @return array
     */
    public function getAllByModel(Model $model)
    {
        return $this->setRepository->findAllByModel($model);
    }

    /**
     * Get all sets in which the model appears.
     *
     * @param Part $part
     *
     * @return array
     */
    public function getAllByPart(Part $part)
    {
        return $this->setRepository->findAllByPart($part);
    }

    /**
     * Get total count of sets in database.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->setRepository->count();
    }

    public function getPartCount(Set $set, $spare = null, $model = null)
    {
        return $this->inventoryPartRepository->getPartCount($set, $spare, $model);
    }

    /**
     * Get array of all known models in the kit(set) with quantity. Ignores colors and groups parts with the same shape.
     * [
     *    modelNumber => [
     *      'model' => Model,
     *      'quantity => int
     *    ]
     *    ...
     * ].
     *
     * @param Set  $set
     * @param bool $spare If true - add only spare parts, false - add only regular parts, null - add all parts
     *
     * @return array
     */
    public function getModels(Set $set, $spare = null)
    {
        $models = [];

        $inventoryParts = $this->inventoryPartRepository->getAllMatching($set, $spare, true);

        /** @var Inventory_Part $inventoryPart */
        foreach ($inventoryParts as $inventoryPart) {
            if ($model = $inventoryPart->getPart()->getModel()) {
                if (isset($models[$model->getId()])) {
                    $models[$model->getId()]['quantity'] += $inventoryPart->getQuantity();
                } else {
                    $models[$model->getId()] = [
                        'model' => $model,
                        'quantity' => $inventoryPart->getQuantity(),
                    ];
                }
            }
        }

        return $models;
    }

    /**
     * Get array models grouped by color.
     * [
     *    'colorID' => [
     *        'color' => Color,
     *        'models => [
     *             modelNumber => [
     *                 'model' => Model,
     *                 'quantity' => int
     *             ]
     *             ...
     *          ]
     *      ]
     *      ...
     * ].
     *
     * @param Set  $set
     * @param bool $spare If true - add only spare parts, false - add only regular parts, null - add all parts
     *
     * @return array
     */
    public function getModelsGroupedByColor(Set $set, $spare = null)
    {
        $colors = [];

        $inventoryParts = $this->inventoryPartRepository->getAllMatching($set, $spare, true);

        /** @var Inventory_Part $inventoryPart */
        foreach ($inventoryParts as $inventoryPart) {
            if ($model = $inventoryPart->getPart()->getModel()) {
                $color = $inventoryPart->getCOlor();

                if (!isset($colors[$color->getId()]['color'])) {
                    $colors[$color->getId()]['color'] = $color;
                    $colors[$color->getId()]['quantity'] = 0;
                }

                $colors[$color->getId()]['quantity'] += $inventoryPart->getQuantity();

                $colors[$color->getId()]['models'][$model->getId()] = [
                    'model' => $model,
                    'quantity' => $inventoryPart->getQuantity(),
                ];
            }
        }

        return $colors;
    }

    /*
     * @param Set  $set
     * @param bool $spare If true - add only spare parts, false - add only regular parts, null - add all parts
     *
     * @return array
     */
    public function getParts(Set $set, $spare = null, $model = false)
    {
        $parts = [];

        $inventoryParts = $this->inventoryPartRepository->getAllMatching($set, $spare, $model);

        /** @var Inventory_Part $inventoryPart */
        foreach ($inventoryParts as $inventoryPart) {
            if (isset($parts[$inventoryPart->getPart()->getId()])) {
                $parts[$inventoryPart->getPart()->getId()]['quantity'] += $inventoryPart->getQuantity();
            } else {
                $parts[$inventoryPart->getPart()->getId()] = [
                    'part' => $inventoryPart->getPart(),
                    'quantity' => $inventoryPart->getQuantity(),
                ];
            }
        }

        return $parts;
    }
}
