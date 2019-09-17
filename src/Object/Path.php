<?php

namespace App\Object;

use \App\Object\Cell;
use \App\Object\Grid;

class Path
{
    /**
     * StartCell
     * @var Grid
     */
    protected $grid;

    protected $successPathCells;

    /**
     * Constructeur.
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    function goto (Cell $start, Cell $end, $pathCells = []) {
        $pathCells[] = $start;
        if( $start->getStatus() != Cell::STATUS_START ){
          $start->setStatus(Cell::STATUS_OBSERVED);
        }

        // Si le chemin qu'on teste est plus long qu'un des résultats,
        // alors on balance une erreur, inutile d'aller plus loin.
        if( $this->successPathCells && count($this->successPathCells) < count($pathCells) ){
            throw new \Exception('Trop long');
        }

        $aroundCells = $this->grid->getAroundCells($start, $end);
        if ($start->getId() == '7_9'){
          dump( $aroundCells );
        }
        if(!empty($aroundCells)){
            if ($end->isIn($aroundCells)) {
                $this->successPathCells = $pathCells;
            }
            else{
                foreach( $aroundCells as $cell ){
                    try{
                        $this->goto($cell, $end, $pathCells);
                    }
                    catch(\Exception $e){
                        continue;
                    }
                }
            }
        }
        
    }

    public function getSuccessPath(){
        return $this->successPathCells;
    }
}
