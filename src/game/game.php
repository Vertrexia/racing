<?php
if (!defined("__ROOT__")) {
    return;
}

class Game
{
    var $roundFinished = false;

    function center($message)
    {
        echo "CENTER_MESSAGE ".$message."\n";
    }

    function con($message)
    {
        echo "CONSOLE_MESSAGE ".$message."\n";
    }	

    function pm($player, $message)
    {
        echo "PLAYER_MESSAGE ".$message."\n";
    }

    //  custom player message - sends a custom message to that selected player (if they exist)
    //  $langauge_command is the langauge string command in language files to load
    function cpm($player, $langauge_command, $params = array())
    {
        if (count($params) == 0)
            echo "CUSTOM_PLAYER_MESSAGE ".$player." ".$langauge_command."\n";
        else
        {
            $extras = "";
            foreach($params as $param)
            {
                $extras .= $param." ";
            }
            echo "CUSTOM_PLAYER_MESSAGE ".$player." ".$langauge_command." ".$extras."\n";
        }
    }

    //  custom message - sends a custom message to all clients, public message to simplify
    //  $langauge_command is the langauge string command in language files to load
    function cm($langauge_command, $params = array())
    {
        if (count($params) == 0)
            echo "CUSTOM_MESSAGE ".$player." ".$langauge_command."\n";
        else
        {
            $extras = "";
            foreach($params as $param)
            {
                $extras .= $param." ";
            }
            echo "CUSTOM_MESSAGE ".$player." ".$langauge_command." ".$extras."\n";
        }
    }

    function roundBegan()
    {
        global $ar;
        $ar->timer = new Timer;
        $ar->timer->start();

        //	clear all previous race data
        unset($ar->races);
        $ar->races = array();
        $ar->firstTime_ = -1;
        $ar->countdown_ = -1;
        $ar->finishRank = 1;

        $this->roundFinished = false;
    }

    function roundEnded()
    {
        global $ar;

        //	although the timer is stopped, game timer is still running until next round/match starts.
        $ar->timer->stop();
        $this->roundFinished = true;

        if ($ar->zonesCollapseAfterFinish)
        {
            for ($i = 0; $i < count($ar->zones); $i++)
            {
                $zone = $ar->zones[$i];
                if ($zone)
                {
                    echo "COLLAPSE_ZONE ".$zone->name."\n";

                    unset($ar->zones[$i]);
                    $i--;
                }
            }
        }
        
        //  save the collected information
        $ar->r->saveRecords();
        $ar->q->saveQueuers();
        
        //  reset done for rotation per round
        if ($ar->rotation_type == 1)
            $ar->rotation->done = false;
    }

    function respawnPlayer($name, $x, $y, $xdir, $ydir)
    {
        echo "RESPAWN_PLAYER ".$name." ".$x." ".$y." ".$xdir." ".$ydir."\n";
    }

    function killPlayer($name)
    {
        echo "KILL ".$name."\n";
    }
    
    function declareRoundWinner($name)
    {
        echo "DECLARE_ROUND_WINNER ".$name."\n";
    }
}
?>