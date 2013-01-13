<?php
if (!defined("__ROOT__")) {
    return;
}

class Record
{
    var $name;		//	the record owner
    var $time;		//	player's best
    var $rank;		//	current rank in the item

    function __construct($name)
    {
        $this->name = $name;
    }

    //	class related functions
    function recordExists($name)
    {
        global $ar;

        if (count($ar->records) > 0)
        {
            foreach ($ar->records as $record)
            {
                if ($record->name == $name)
                {
                    return true;
                }
            }
        }
        return false;
    }

    function getRecord($name)
    {
        global $ar;

        if (count($ar->records) > 0)
        {
            foreach ($ar->records as $record)
            {
                if ($record->name == $name)
                {
                    return $record;
                }
            }
        }
        return false;
    }

    //	adding new record
    function newRecord($name, $time)
    {
        global $ar;

        if (count($ar->records) > 0)
        {
            //	back up
            $strRecords = $ar->records;

            //	lets set
            $pRecord = new Record($name);
            if ($pRecord)
            {
                $pRecord->time = $time;
            }
            $set = false;

            //	clear current list
            unset($ar->records);
            $ar->records = array();

            //	put them back in, along with new record
            $rank = 0;
            foreach ($strRecords as $strRecord)
            {
                $rank++;
                if (($time <= $strRecord->time) && !$set)
                {
                    $pRecord->rank = $rank;
                    $rank++;
                    $strRecord->rank = $rank;

                    $ar->records[] = $pRecord;
                    $ar->records[] = $strRecord;

                    $set = true;

                    $ar->game->cpm($pRecord->screen_name, "race_personal_record", array($pRecord->time));

                    if ($rank > 1)
                    {
                        $ar->game->cpm($pRecord->screen_name, "race_ladder_rise", array($pRecord->rank));
                    }
                    else
                    {
                        $ar->game->cm("race_ladder_best", array($pRecord->screen_name, $pRecord->time));
                    }

                    $ar->game->cpm($strRecord->screen_name, "race_ladder_drop", array($pRecord->screen_name, $strRecord->rank));
                }
                else
                {
                    $strRecord->rank = $rank;
                    $ar->records[] = $strRecord;

                    if ($set)
                    {
                        $ar->game->cpm($strRecord->screen_name, "race_ladder_drop", array($pRecord->screen_name, $strRecord->rank));
                    }
                }
            }
        }
    }

    //	adjusting records
    function adjustRecords($pRecord, $newTime)
    {
        global $ar;

        if (count($ar->records) > 0)
        {
            //	back up
            $strRecords = $ar->records;

            //	lets set
            if ($pRecord)
            {
                $pRecord->time = $newTime;
            }
            $set = false;

            //	clear current list
            unset($ar->records);
            $ar->records = array();

            //	put them back in, along with new record
            $rank = 0;
            foreach ($strRecords as $strRecord)
            {
                $rank++;
                if (($time <= $strRecord->time) && !$set)
                {
                    $pRecord->rank = $rank;
                    $rank++;
                    $strRecord->rank = $rank;

                    $ar->records[] = $pRecord;
                    $ar->records[] = $strRecord;

                    $set = true;

                    $ar->game->cpm($pRecord->screen_name, "race_personal_record", array($pRecord->time));

                    if ($rank > 1)
                    {
                        $ar->game->cpm($pRecord->screen_name, "race_ladder_rise", array($pRecord->rank));
                    }
                    else
                    {
                        $ar->game->cm("race_ladder_best", array($pRecord->screen_name, $pRecord->time));
                    }

                    $ar->game->cpm($strRecord->screen_name, "race_ladder_drop", array($pRecord->screen_name, $strRecord->rank));
                }
                else
                {
                    $strRecord->rank = $rank;
                    $ar->records[] = $strRecord;

                    if ($set)
                    {
                        $ar->game->cpm($strRecord->screen_name, "race_ladder_drop", array($pRecord->screen_name, $strRecord->rank));
                    }
                }
            }
        }
    }

    function loadRecords($item)
    {
        global $ar;

        if (count($ar->records) > 0)
        {
            unset($ar->records);
            $ar->records = array();
        }

        $fpath = $ar->path.$ar->recordsDir.$item.".txt";

        //	using addslashes() function to ensure no bugs with reading path
        $fpath = addslashes($fpath);
        if (file_exists($fpath))
        {
            $file = fopen($fpath, "r");
            if ($file)
            {
                $rank = 0;
                while (!feof($file))
                {
                    $line = fread($file);
                    if ($line != "")
                    {
                        $lineExt = explode(" ", $line);
                        $record = new Record($lineExt[0]);

                        if ($record)
                        {
                            $rank++;
                            $record->time = $lineExt[1];
                            $record->rank = $rank;

                            $ar->records[] = $record;
                        }
                    }
                }
                fclose($file);
            }
        }
    }

    function saveRecords()
    {
        global $ar;

        //	saving records
        $fpath = $ar->path.$ar->recordsDir.$ar->rotation->item.".txt";

        //	using addslashes() function to ensure no bugs with reading path
        $fpath = addslashes($fpath);
        $file = fopen($fpath, "w+");
        if ($file)
        {
            if (count($ar->records) > 0)
            {
                foreach ($ar->records as $record)
                {
                    if ($record)
                    {
                        fwrite($file, $record->name." ".$record->time."\n");
                    }
                }
            }

            fclose($file);
        }
    }
}
?>