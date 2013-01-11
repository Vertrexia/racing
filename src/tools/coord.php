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
}
?>