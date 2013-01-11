<?php
if (!defined("__ROOT__")) {
    return;
}

class Rotation
{
    var $items;
    var $itemKey = 0;
    var $item = "";

    function rotate()
    {
        global $ar;

        if ($this->itemKey >= count($this->items)) {
            $this->itemKey = 0;
        } else {
            //	make sure rotation is enabled
            if ($ar->rotation_type > 0) {
                if ($ar->rotation_type == 1) {
                    $this->item = $this->items[$this->itemKey];
                } elseif ($ar->rotation_type == 2) {
                    $this->itemKey = int(rand(0, count($this->item)));
                    $this->item = $this->items[$this->itemKey];
                }

                if ($ar->rotation_load == 0) {
                    echo "INCLIDE ".$this->item."\n";
                } elseif ($ar->rotation_load == 1) {
                    echo "SINCLUDE "$this->item."\n";
                } elseif ($ar->rotation_load == 2) {
                    echo "RINCLUDE "$this->item."\n";
                }
                
                $this->itemKey++;

                $ar->game->con("Reading from ".$this->item);
                $ar->r->loadRecords($this->item);
            }
        }
    }

    function addRotation()
    {
        global $ar;
        foreach ($ar->rotations as $item) {
            $this->items[] = $item;
        }
    }
}
?>