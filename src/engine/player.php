<?php
if (!defined("__ROOT__")) {
    return;
}

class Player
{
    //	personal info
    var $name;
    var $screen_name;
    var $color_name;

    //	server info
    var $cycle;
    var $records;
    var $queuer;

    var $isHuman = false;
    var $access_level = 20;

    function __construct($name)
    {
        $this->name = $name;
    }

    function Delete()
    {
        unset($this);
    }

    //	players
    function playerExists($name)
    {
        global $ar;
        if (count($ar->players) > 0)
        {
            foreach ($ar->players as $p)
            {
                if ($p->name == $name)
                    return true;
            }
            return false;
        }
    }

    function getPlayer($name)
    {
        global $ar;
        if (count($ar->players) > 0)
        {
            foreach ($ar->players as $p)
            {
                if (contains($p->name, $name))
                    return $p;
            }
        }
        return false;
    }

    function playerEntered($name, $screenName, $human)
    {
        global $ar;
        if (!$this->playerExists($name))
        {
            $player = new Player($name);

            if ($player)
            {
                $player->isHuman = $human;
                $player->screen_name = $screenName;

                $record = $ar->r->getRecord($name);
                if ($record && $human)
                    $player->record = $record;

                $queuer = $ar->q->getQueuer($name);
                if ($queuer && $human)
                    $player->queuer = $queuer;
                elseif (!$queuer && $human)
                {
                    $queuer = new Queuer($player->name);
                    if ($queuer)
                    {
                        $queuer->amount = $ar->queue_give;
                        $ar->queuers[] = $queuer;
                    }
                }
                else
                     $player->queuer = null;

                $ar->players[] = $player;
            }
        }
        else
        {
            $player = $this->getPlayer($name);
            if ($player)
            {
                $player->isHuman = $human;
                $player->screen_name = $screenName;

                $record = $ar->r->getRecord($name);
                if ($record && $human)
                    $player->record = $record;

                $queuer = $ar->q->getQueuer($name);
                if ($queuer && $human)
                    $player->queuer = $queuer;
                elseif (!$queuer && $human)
                {
                    $queuer = new Queuer($player->name);
                    if ($queuer)
                    {
                        $queuer->amount = $ar->queue_give;
                        $ar->queuers[] = $queuer;
                    }
                }
                else
                     $player->queuer = null;                
            }
        }
    }

    function playerRenamed($old, $new, $screenName)
    {
        global $ar;
        $player = $this->getPlayer($old);

        if ($player)
        {
            $player->name = $new;
            $player->screen_name = $screenName;

            //	fetch records and queues related to the new name
            //	this is due to people hacking into other accounts

            $record = $ar->r->getRecord($name);
            if ($record && $player->isHuman)
                $player->record = $record;
            else
                $player->record = null;

            $queuer = $ar->q->getQueuer($name);
            if ($queuer && $player->isHuman)
                $player->queuer = $queuer;
            elseif (!$queuer && $human)
            {
                $queuer = new Queuer($player->name);
                if ($queuer)
                {
                    $queuer->amount = $ar->queue_give;
                    $ar->queuers[] = $queuer;
                }
            }
            else
                $player->queuer = null;
        }
    }

    function playerLeft($name)
    {
        global $ar;
        if (count($ar->players) > 0)
        {
            foreach ($ar->players as $key => $p)
            {
                if ($p->name == $name)
                {
                    //	remove player from the list
                    $p->Delete();
                    unset($ar->players[$key]);
                    break;
                }
            }
        }
    }
};
?>