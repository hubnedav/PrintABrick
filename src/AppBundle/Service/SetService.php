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

            $inventoryParts = $this->inventoryPartRepository->findAllBySetNumber($set->getNumber(), $spare, true);

            /** @var Inventory_Part $inventoryPart */
            foreach ($inventoryParts as $inventoryPart) {
                if ($model = $inventoryPart->getPart()->getModel()) {
                    if (isset($models[$model->getNumber()])) {
                        $models[$model->getNumber()]['quantity'] += $inventoryPart->getQuantity();
                    } else {
                        $models[$model->getNumber()] = [
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

            $inventoryParts = $this->inventoryPartRepository->findAllBySetNumber($set->getNumber(), $spare, true);

            /** @var Inventory_Part $inventoryPart */
            foreach ($inventoryParts as $inventoryPart) {
                if ($model = $inventoryPart->getPart()->getModel()) {
                    $color = $inventoryPart->getColor();

                    if (!isset($models[$model->getNumber()]['model'])) {
                        $models[$model->getNumber()]['model'] = $model;
                    }

                    if (isset($models[$model->getNumber()]['colors'][$color->getId()])) {
                        $models[$model->getNumber()]['colors'][$color->getId()]['quantity'] += $inventoryPart->getQuantity();
                    } else {
                        $models[$model->getNumber()]['colors'][$color->getId()] = [
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

            $inventoryParts = $this->inventoryPartRepository->findAllBySetNumber($set->getNumber(), $spare, true);

            /** @var Inventory_Part $inventoryPart */
            foreach ($inventoryParts as $inventoryPart) {
                if ($model = $inventoryPart->getPart()->getModel()) {
                    $color = $inventoryPart->getCOlor();

                    if (!isset($colors[$color->getId()]['color'])) {
                        $colors[$color->getId()]['color'] = $color;
                        $colors[$color->getId()]['quantity'] = 0;
                    }

                    $colors[$color->getId()]['quantity'] += $inventoryPart->getQuantity();

                    if (isset($colors[$color->getId()]['models'][$model->getNumber()])) {
                        $colors[$color->getId()]['models'][$model->getNumber()]['quantity'] += $inventoryPart->getQuantity();
                    } else {
                        $colors[$color->getId()]['models'][$model->getNumber()] = [
                            'model' => $model,
                            'quantity' => $inventoryPart->getQuantity(),
                        ];
                    }
                }
            }

            return $colors;
        }
    }
