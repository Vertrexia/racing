<?php
if (!defined("__ROOT__")) {
    return;
}

class InvalidCommand
{
    var $caller;             //  name of the person issuing the command
    var $access_level;       //  the player's access level
    var $command;            //  the command being called
    var $params;             //  any extra data they added to the command

    //  contains the list of commands allowed to be executed
    //  how it works: "command" => "access_level"
    var $commands = array(
                            "/q" => 20,    //  for item queing
                            "/r" => 20,    //  for checking your rank in the current config file
                            "/c" => 0      //  can change player's login {MIGHT NOT BE GOOD IDEA}
                          );
    
    //  checks if the calling command is one of the valid ones stored above
    function commandAllowed()
    {
        return array_key_exists($this->command, $this->commands);
    }
    
    function getRequiredLevel()
    {
        return $this->commands[$this->command];
    }
    
    //  executing code for invalid commands
    function execute()
    {
        global $ar;
        if ($this->commandAllowed())
        {
            $required_level = $this->getRequiredLevel();
            
            //  make sures the caller's access level is either equal to or lower than the required level
            if ($this->access_level <= $required_level)
            {
                if ($this->command == "/c")
				{
					//	code later, maybe...
				}
				elseif ($this->command == "/q")
				{
                    $item = "";
                    if (contains($this->params, " "))
                    {
                        $ext = explode($this->params, " ");
                        $item = $ext[0];
                    }
                    else
                        $item = $this->params;
                    
                    $itemsFound = array();
                    //  making sure item actually exists within the rotation list
                    if (count($ar->rotation->items) > 0)
                    {
                        foreach ($ar->rotation->items as $selItem)
                        {
                            if (contains($selItem, $item))
                            {
                                $itemsFound[] = $selItem;
                            }
                        }
                    }
                    
                    //  if no matches were found with that item in rotation
                    if (count($itemsFound) == 0)
                    {
                        $player = $ar->p->getPlayer($this->caller);
                        if ($player)
                        {
                            $ar->game->cpm($player->screen_name, "race_queue_item_notfound", array($item));
                        }
                    }
                    //  if matches were found from the rotation related to the item
                    else
                    {
                        //  oh good, only one item found
                        if (count($itemsFound) == 1)
                        {
                            $player = $ar->p->getPlayer($this->caller);
                            if ($player)
                            {
                                //  if queue allows copies
                                if ($ar->queue_copies)
                                {
                                    //  add item to the queue list
                                    $ar->queue_items[] = $item;
                                    
                                    //  announcing the addition of the item and the one responsible for it
                                    $ar->game->cm("race_queue_item_added", array($player->screen_name, $item));
                                }
                                //  if queue doesn't allow for copies
                                else
                                {
                                    if (count($ar->queue_items) > 0)
                                    {
                                        $itemFound = false;
                                    }
                                    else
                                    {
                                        //  add item to the queue list
                                        $ar->queue_items[] = $item;
                                        
                                        //  announcing the addition of the item and the one responsible for it
                                        $ar->game->cm("race_queue_item_added", array($player->screen_name, $item));
                                    }
                                }
                            }
                        }
                        //  wow, many items were found
                        else
                        {
                            $player = $ar->p->getPlayer($this->caller);
                            if ($player)
                            {
                                $ar->game->cpm($player->screen_name, "race_queue_item_manyfound", array($item));
                            }                            
                        }
                    }
				}
            }
        }
    }
}
?>