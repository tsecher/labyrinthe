<?php

namespace App\Object;

/**
 * Cellule d'une grille.
 *
 * Comprends un status d'observabilité.
 */
class Cell
{
    /**
     * Liste des status disponibles.
     */
    const STATUS_FREE = 0;
    const STATUS_OBSTACLE = 1;
    const STATUS_OBSERVED = 2;
    const STATUS_START = 3;
    const STATUS_END = 4;
    const STATUS_OK = 5;

    /**
     * Statu courant.
     *
     * @var int
     */
    protected $status = 0;

    protected $coordinates = [];

    /**
     * Constructeur.
     *
     * @param int[] $coordinates
     * @param int $status
     */
    public function __construct(array $coordinates, $status = self::STATUS_FREE)
    {
        $this->coordinates = $coordinates;
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getId()
    {
        return implode($this->coordinates, '_');
    }

    public function getCoordinates()
    {
        return $this->coordinates;
    }

    public function isIn(array $cells){
      $data = array_map(function(Cell $cell){
        return $cell->getId();
      }, $cells);
        return in_array($this->getId(), $data );

    }

}
