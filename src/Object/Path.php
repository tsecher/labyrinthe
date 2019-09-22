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
        $this->filter($cells, [
            Cell::STATUS_FREE,
            Cell::STATUS_END,
            Cell::STATUS_OBSTACLE
        ]);

        // On trie par priorité :
        $this->sortByDistance($cells, $destination);


        // Si la prochaine la plus proche de la destination (NO) est un obstacle, alors on va voir
        // la plus proche libre de (NO).
        /** @var Cell $nextCell */
        if ($nextCell = reset($cells)) {
            if (in_array($nextCell->getStatus(), [
                Cell::STATUS_OBSTACLE,
            ])) {
                unset($cells[0]);
                $this->sortByDistance($cells, $nextCell);
            }

            $this->filter($cells, [
                Cell::STATUS_FREE,
                Cell::STATUS_END,
                Cell::STATUS_OBSERVED
            ]);
        }


//            if ($currentCell->getId() == '16_8'){
//                dump( $cells );exit;
//            }

    }

    /**
     * Ordonne par distance.
     *
     * @param array $cells
     * @param \App\Object\Cell $ref
     */
    protected function sortByDistance(array &$cells, Cell $ref) {
        // On trie par priorité :
        usort($cells, function (Cell $a, Cell $b) use ($ref) {
            $distanceA = $this->grid->getDistance($a, $ref);
            $distanceB = $this->grid->getDistance($b, $ref);

            if ($distanceA > $distanceB) {
                return 1;
            }
            if ($distanceA < $distanceB) {
                return -1;
            }
            return 0;
        });
    }

    /**
     * Filtre la liste de cellule.
     *
     * @param array $cells
     * @param array $keep
     */
    protected function filter(array &$cells, array $keep) {
        // On trie les cellules.
        $cells = array_filter($cells, function (Cell $cell = NULL) use ($keep) {
            return $cell && in_array($cell->getStatus(), $keep);
        });
    }
}
