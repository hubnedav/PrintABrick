<?php

    namespace AppBundle\Service;

    use AppBundle\Entity\Rebrickable\Inventory_Part;
    use AppBundle\Entity\Rebrickable\Set;
    use AppBundle\Repository\Rebrickable\Inventory_PartRepository;
    use AppBundle\Repository\Rebrickable\PartRepository;

    class SetService
    {
        /** @var Inventory_PartRepository */
        private $inventoryPartRepository;

        /**
         * SetService constructor.
         * @param Inventory_PartRepository $inventoryPartRepository
         */
        public function __construct(Inventory_PartRepository $inventoryPartRepository)
        {
            $this->inventoryPartRepository = $inventoryPartRepository;
        }


        public function getUniqueModelCount(Set $set) {

        }

        public function getModels(Set $set)
        {
            $models = [];

            $inventoryParts = $this->inventoryPartRepository->findAllRegularBySetNumber($set->getNumber());

            /** @var Inventory_Part $inventoryPart */
            foreach ($inventoryParts as $inventoryPart) {
                $model = $inventoryPart->getPart()->getModel();
                $color = $inventoryPart->getColor();
                if($model) {
                    $models[$model->getNumber()]['model'] = $model;

                    $quantity = 0;
                    if(isset($models[$model->getNumber()]['colors'][$color->getId()]['quantity'])) {
                        $quantity = $models[$model->getNumber()]['colors'][$color->getId()]['quantity'];
                    }

                    $models[$model->getNumber()]['colors'][$color->getId()] = [
                        'color' => $color,
                        'quantity' => $quantity+$inventoryPart->getQuantity()
                    ];
                }
            }

            return $models;
        }

        public function getSpareModels(Set $set)
        {
            $models = [];

            $inventoryParts = $this->inventoryPartRepository->findAllSpareBySetNumber($set->getNumber());

            /** @var Inventory_Part $inventoryPart */
            foreach ($inventoryParts as $inventoryPart) {
                $model = $inventoryPart->getPart()->getModel();
                $color = $inventoryPart->getColor();
                if($model) {
                    $models[$model->getNumber()]['model'] = $model;

                    $quantity = 0;
                    if(isset($models[$model->getNumber()]['colors'][$color->getId()]['quantity'])) {
                        $quantity = $models[$model->getNumber()]['colors'][$color->getId()]['quantity'];
                    }

                    $models[$model->getNumber()]['colors'][$color->getId()] = [
                        'color' => $color,
                        'quantity' => $quantity+$inventoryPart->getQuantity()
                    ];
                }
            }

            return $models;
        }
    }