<?php
if (!defined("__ROOT__")) {
    return;
}

class Queuer
{
    var $name;
    var $amount;
    var $current;

    function __construct($name)
    {
        $this->name = $name;
    }

    function queuerexists($name)
    {
        global $ar;
        if (count($ar->queuers) > 0)
        {
            foreach ($ar->queuers as $queuer)
            {
                if ($queuer->name == $name)
                {
                    return true;
                }
            }
        }
        return false;
    }

    function getQueuer($name)
    {
        global $ar;
        if (count($ar->queuers) > 0)
        {
            foreach ($ar->queuers as $queuer)
            {
                if ($queuer->name == $name)
                {
                    return $queuer;
                }
            }
        }
        return false;
    }

    //	load the data onto the server
    function loadQueuers()
    {
        global $ar;

        //	loading queuers
        $queFilePath = $ar->path.$ar->queueFile;

        //	using addslashes() function to ensure no bugs with reading path
        $queFilePath = addslashes($queFilePath);
        if (file_exists($queFilePath))
        {
            $file = fopen($queFilePath, "r");
            if ($file)
            {
                while (!feof($file))
                {
                    $line = fread($fopen);
                    if ($line != "")
                    {
                        $lineExt = explode(" ", $line);
                        $queuer = new Queuer($lineExt[0]);

                        if ($queuer)
                        {
                            $queuer->amount = $lineExt[2];
                            $queuer->current = $lineExt[1];
                            
                            $ar->queuers[] = $queuer;
                        }
                    }
                }
                fclose($file);
            }
        }
    }

    function saveQueuers()
    {
        global $ar;

        //	saving queuers
        $queFilePath = $ar->path.$ar->queueFile;

        //	using addslashes() function to ensure no bugs with reading path
        $queFilePath = addslashes($queFilePath);
        $file = fopen($queFilePath, "w+");
        if ($file)
        {
            if (count($ar->queuers) > 0)
            {
                foreach ($ar->queuers as $queuer)
                {
                    if ($queuer)
                    {
                        fwrite($file, $queuer->name." ".$queuer->current." ".$queuer->amount."\n");
                    }
                }
            }

            fclose($file);
        }
    }
};
?>