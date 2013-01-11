<?php
if (!defined("__ROOT__")) {
    return;
}

class Zone
{
    var $name;

    var $spawn_pos;

    function zoneCreated($name, $x, $y)
    {
        global $ar;

        $zone = new Zone;
        $zone->name = $name;
        $zone->spawn_pos = new Coord($x, $y);

        $ar->zones[] = $zone;
    }
};
?>