<?php
if (!defined("__ROOT__")) {
    return;
}

class Coord
{
    var $x;
    var $y;

    function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
    
    //  cheking if the given coord points match with this object's points
    function equals($coord)
    {
        if ($coord)
        {
            if (($this->x == $coord->x) && ($this->y == $coord->y))
                return true;
        }
        return false;
    }
    
    //  subtract points by the given coord class object
    function minus($coord)
    {
        if ($coord)
        {
            $newX = $this->x - $coord->x;
            $newY = $this->y - $coord->y;
            $newCoord = new Coord($newX, $newY);
            return $newCoord;
        }
    }
    
    //  add points with the given coord object
    function plus($coord)
    {
        if ($coord)
        {
            $newX = $this->x + $coord->x;
            $newY = $this->y + $coord->y;
            $newCoord = new Coord($newX, $newY);
            return $newCoord;
        }
    }
    
    function NormSquared()
    {
        $x = $this->x;
        $y = $this->y;
        
        return ($x*$x + $y*$y);
    }
}
?>