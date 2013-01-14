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
                            "/queue"    => 20,    //  for item queing
                            "/rank"     => 20,    //  for checking your rank in the current config file
                            "/edit"     => 0,     //  can change player's login rank {MIGHT NOT BE GOOD IDEA}
                            "/save"     => 2,     //  save all collected data to files
                            "/rload"    => 2      //  reload the rotation items
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
        $player = $ar->p->getPlayer($this->caller);
        if ($this->commandAllowed())
        {
            $required_level = $this->getRequiredLevel();
            
            //  make sure the caller's access level is either equal to or lower than the required level
            if ($this->access_level <= $required_level)
            {
                if ($this->command == "/rload")
                {
                    
                }
                elseif ($this->command == "/save")
                {                    
                    if ($player)
                    {
                        //  save the collected information
                        $ar->r->saveRecords();
                        $ar->q->saveQueuers();
                        
                        $ar->game->cpm($player->screen_name, "race_data_saved");
                    }
                }
                elseif ($this->command == "/edit")
				{
					//	code later, maybe...
                    //  still having doubts whether to code or not
				}
				elseif ($this->command == "/queue")
				{
                    $item = "";
                    if (contains($this->params, " "))
                    {
                        $ext = explode($this->params, " ");
                        $item = $ext[0];
                    }
                    else
                        $item = $this->params;
                    
                    //  if item is blank, don't continue
                    if ($item == "")
                        return;
                    
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
                            if ($player)
                            {
                                //  if queue allows copies
                                if ($ar->queue_copies)
                                {
                                    $queuer = $player->queuer;
                                    if ($queuer)
                                    {
                                        if ($queuer->current > 0)
                                        {
                                            //  add item to the queue list
                                            $ar->queue_items[] = $item;
                                            
                                            //  announcing the addition of the item and the one responsible for it
                                            $ar->game->cm("race_queue_item_added", array($player->screen_name, $item));
                                            
                                            //  deplete the number of queues they can perform
                                            $queuer->current--;
                                        }
                                    }
                                }
                                //  if queue doesn't allow for copies
                                else
                                {
                                    if (count($ar->queue_items) > 0)
                                    {
                                        $itemFound = false;
                                        foreach ($ar->queue_items as $qItem)
                                        {
                                            if ($qItem == $item)
                                                $itemFound = true;
                                        }
                                        
                                        if (!$itemFound)
                                        {
                                            $queuer = $player->queuer;
                                            if ($queuer)
                                            {
                                                if ($queuer->current > 0)
                                                {
                                                    $ar->queue_items[] = $item;
                                                    
                                                    $ar->game->cm("race_queue_item_added", array($player->screen_name, $item));
                                                    
                                                    $queuer->current--;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $ar->game->cpm($player->screen_name, "race_queue_item_nocopies", array($item));
                                        }
                                    }
                                    else
                                    {
                                        $queuer = $player->queuer;
                                        if ($queuer)
                                        {
                                            if ($queuer->current > 0)
                                            {
                                                $ar->queue_items[] = $item;
                                                
                                                $ar->game->cm("race_queue_item_added", array($player->screen_name, $item));
                                                
                                                $queuer->current--;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //  wow, many items were found
                        else
                        {
                            if ($player)
                            {
                                $ar->game->cpm($player->screen_name, "race_queue_item_manyfound", array($item));
                            }
                        }
                    }
				}
            }
        }
        //  send player a message about the valid commands to use
        else
        {
            if ($player)
            {
                $commands = array_keys($this->commands);
                
                //  should have from \1 to \4 in lagnuage command string
                $ar->game->cpm($player->screen_name, "race_valid_commands", $commands);
            }
        }
    }
}
?>