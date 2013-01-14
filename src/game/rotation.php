<?php
if (!defined("__ROOT__")) {
    return;
}

class Rotation
{
    var $items = array();
    var $itemKey = 0;
    var $item = "";
    var $done = false;

    function rotate()
    {
        global $ar;
        
        //  make sure there are items in the rotation bank to rotate
        if (count($this->items) == 0)
        {
            $ar->game->con("There are no items in the rotation bank to perform any rotation actions.");
            return;
        }

        if ($this->itemKey >= count($this->items))
        {
            $this->itemKey = 0;
        }
        
        //	make sure rotation is enabled
        if ($ar->rotation_type > 0)
        {
            $this->item = $this->items[$this->itemKey];

            if ($ar->rotation_load == 0)
            {
                echo "INCLUDE ".$this->item."\n";
            }
            elseif ($ar->rotation_load == 1)
            {
                echo "SINCLUDE ".$this->item."\n";
            }
            elseif ($ar->rotation_load == 2)
            {
                echo "RINCLUDE ".$this->item."\n";
            }
            
            $this->itemKey++;

            $ar->game->con("Reading from ".$this->item);
            $ar->r->loadRecords($this->item);
        }
    }

    function init()
    {
        global $ar;
        
        if ((count($this->items) > 0))
        {
            unset($this->items);
            $this->items = array();
        }
        
        $rotFile = $ar->path.$ar->rotationFile;
        if (file_exists($rotFile))
        {
            $contents = file_get_contents($rotFile);
            $contents = explode("\n");
            
            if (!empty($contents) && (count($contents) > 0))
            {
                foreach($contents as $item)
                {
                    if ($item != "")
                        $this->items[] = $item;
                }
            }
        }
    }
}
?>