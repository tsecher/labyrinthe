<?php

    namespace App\Object;

    /**
     * Generateur de grille
     */
    class Grid {
        /**
         * Largeur de grille.
         *
         * @var int
         */
        protected $width;

        /**
         * Hauteur de grille.
         *
         * @var int
         */
        protected $height;

        /**
         * Grille.
         *
         * @var \App\Object\Cell[]
         */
        protected $grid;

        /**
         * Constructeur de grille.
         *
         * @param int $width
         * @param int $height
         */
        public function __construct($width, $height) {
            $this->width = $width;
            $this->height = $height;
        }

        /**
         * Génère la grille.
         */
        public function generate($obstacleCount = 0) {
            $this->grid = [];

            for ($i = 0; $i < ($this->width * $this->height); $i++) {
                $this->grid[$i] = new Cell(
                    [
                        floor($i / $this->width),
                        $i % ($this->width),
                    ],
                    Cell::STATUS_FREE
                );
            }
        }

        /**
         * Retourne la cellule à la position.
         *
         * @param int $x
         * @param int $y
         *
         * @return \App\Object\Cell
         */
        public function getCell(array $coordinates) {
            if (
                reset($coordinates) < $this->width && end($coordinates) < $this->height
                && reset($coordinates) > -1 && end($coordinates) > -1
            ) {
                return $this->grid[reset($coordinates) * $this->width + end($coordinates)];
            }
            return NULL;
        }

        /**
         * Retourne les cellules autour de celle passée.
         * Avec en priorité la plus proche de la destination.
         *
         */
        public function getAroundCells(Cell $cell, Cell $destination, Cell $from = null) {
            // récupération de la liste des cellules:
            $cells = [];
            $refCoordinates = $cell->getCoordinates();

            $cells[] = $this->getCell([
                reset($refCoordinates) - 1,
                end($refCoordinates)
            ]);
            $cells[] = $this->getCell([
                reset($refCoordinates) + 1,
                end($refCoordinates)
            ]);
            $cells[] = $this->getCell([
                reset($refCoordinates),
                end($refCoordinates) - 1
            ]);
            $cells[] = $this->getCell([
                reset($refCoordinates),
                end($refCoordinates) + 1
            ]);

            return $cells;
        }

        public function getDistance(Cell $cellA, Cell $cellB) {
            $coordA = $cellA->getCoordinates();
            $coordB = $cellB->getCoordinates();
            $a = reset($coordB) - reset($coordA);
            $b = end($coordB) - end($coordA);

            return $a * $a + $b * $b;
        }

        public function width() {
            return $this->width;
        }

        public function height() {
            return $this->height;
        }

        public function randomObstacle($nb) {
            for ($i = 0; $i < $nb; $i++) {
                $rand = rand(0, count($this->grid));
                $this->getCell([
                    floor($rand / $this->width),
                    $rand % $this->height
                ])->setStatus(Cell::STATUS_OBSTACLE);
            }
        }

        public function getCellById($id) {
            return $this->getCell(explode('_', $id));
        }

    }
