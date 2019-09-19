<?php

    namespace App\Object;

    use \App\Object\Cell;
    use \App\Object\Grid;

    class Path {
        /**
         * StartCell
         * @var Grid
         */
        protected $grid;

        protected $successPathCells;

        /**
         * Constructeur.
         */
        public function __construct(Grid $grid) {
            $this->grid = $grid;
        }

        function goto(Cell $start, Cell $end, $pathCells = []) {
            $pathCells[] = $start;
            if ($start->getStatus() != Cell::STATUS_START) {
                $start->setStatus(Cell::STATUS_OBSERVED);
            }

            // Si le chemin qu'on teste est plus long qu'un des résultats,
            // alors on balance une erreur, inutile d'aller plus loin.
            if ($this->successPathCells && count($this->successPathCells) < count($pathCells)) {
                throw new \Exception('Trop long');
            }

            // On récupère et filtre les cellules.
            $aroundCells = $this->grid->getAroundCells($start, $end, end($pathCells));
            $this->filterCells($aroundCells, $end, end($pathCells), $start);

            // On parcours les cellules
            if (!empty($aroundCells)) {
                if ($end->isIn($aroundCells)) {
                    $this->successPathCells = $pathCells;
                }
                else {
                    foreach ($aroundCells as $cell) {
                        try {
                            $this->goto($cell, $end, $pathCells);
                        }
                        catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            }

        }

        /**
         * Retourne la liste des cell parcourues.
         *
         * @return mixed
         */
        public function getSuccessPath() {
            return $this->successPathCells;
        }

        /**
         * Ordonne les cellules par ordre de priorité à traiter.
         *
         * @param array $aroundCells
         */
        protected function filterCells(array &$cells, Cell $destination, Cell $from, Cell $currentCell) {
            // On trie les cellules.
            $cells = array_filter($cells, function (Cell $cell = NULL) {
                return $cell && in_array($cell->getStatus(), [
                        Cell::STATUS_FREE,
                        Cell::STATUS_END
                        //          Cell::STATUS_OBSERVED,
                    ]);
            });

            // On trie par priorité :
            usort($cells, function (Cell $a, Cell $b) use ($destination, $from, $currentCell) {

                $distanceA = $this->grid->getDistance($a, $destination);
                $distanceB = $this->grid->getDistance($b, $destination);

                if ($distanceA > $distanceB) {
                    return 1;
                }
                if ($distanceA < $distanceB){
                    return -1;
                }

                if ($a->sameLine($from)){
                    return 1;
                }
                if ($b->sameLine($from)){
                    return -1;
                }

                return 0;
            });

        }
    }
