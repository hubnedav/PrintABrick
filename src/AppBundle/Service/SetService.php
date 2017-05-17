<?php

namespace AppBundle\Service;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Repository\Rebrickable\Inventory_PartRepository;

class SetService
{
    /** @var Inventory_PartRepository */
        private $inventoryPartRepository;

        /**
         * SetService constructor.
         *
         * @param Inventory_PartRepository $inventoryPartRepository
         */
        public function __construct(Inventory_PartRepository $inventoryPartRepository)
        {
            $this->inventoryPartRepository = $inventoryPartRepository;
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

            $inventoryParts = $this->inventoryPartRepository->findAllBySetNumber($set->getId(), $spare, true);

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
         * Get array of all known models in the kit(set).
         * [
         *    modelNumber => [
         *      'model' => Model,
         *      'colors => [
         *         colorID => [
         *              'color' => Color,
         *              'quantity => int
         *          ]
         *          ...
         *       ]
         *    ]
         *    ...
         * ].
         *
         * @param Set  $set
         * @param bool $spare If true - add only spare parts, false - add only regular parts, null - add all parts
         *
         * @return array
         */
        public function getModelsWithColors(Set $set, $spare = null)
        {
            $models = [];

            $inventoryParts = $this->inventoryPartRepository->findAllBySetNumber($set->getId(), $spare, true);

            /** @var Inventory_Part $inventoryPart */
            foreach ($inventoryParts as $inventoryPart) {
                if ($model = $inventoryPart->getPart()->getModel()) {
                    $color = $inventoryPart->getColor();

                    if (!isset($models[$model->getId()]['model'])) {
                        $models[$model->getId()]['model'] = $model;
                    }

                    if (isset($models[$model->getId()]['colors'][$color->getId()])) {
                        $models[$model->getId()]['colors'][$color->getId()]['quantity'] += $inventoryPart->getQuantity();
                    } else {
                        $models[$model->getId()]['colors'][$color->getId()] = [
                            'color' => $color,
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

            $inventoryParts = $this->inventoryPartRepository->findAllBySetNumber($set->getId(), $spare, true);

            /** @var Inventory_Part $inventoryPart */
            foreach ($inventoryParts as $inventoryPart) {
                if ($model = $inventoryPart->getPart()->getModel()) {
                    $color = $inventoryPart->getCOlor();

                    if (!isset($colors[$color->getId()]['color'])) {
                        $colors[$color->getId()]['color'] = $color;
                        $colors[$color->getId()]['quantity'] = 0;
                    }

                    $colors[$color->getId()]['quantity'] += $inventoryPart->getQuantity();

                    if (isset($colors[$color->getId()]['models'][$model->getId()])) {
                        $colors[$color->getId()]['models'][$model->getId()]['quantity'] += $inventoryPart->getQuantity();
                    } else {
                        $colors[$color->getId()]['models'][$model->getId()] = [
                            'model' => $model,
                            'quantity' => $inventoryPart->getQuantity(),
                        ];
                    }
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

            $inventoryParts = $this->inventoryPartRepository->findAllBySetNumber($set->getId(), $spare, $model);

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
