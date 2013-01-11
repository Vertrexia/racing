<?php
if (!defined("__ROOT__")) {
    return;
}

//	string beginning with
function startswith($str, $find)
{
    if (strlen($find) > strlen($str)) {
        return false;
    }

    if (strlen($find) == 0) {
        return false;
    }

    $isThis = substr($str, 0, strlen($find));
    if ($isThis == $find) {
        return true;
    }
    return false;
}

//	string ends with
function endswith($str, $find)
{
    if (strlen($find) > strlen($str)) {
        return false;
    }

    if (strlen($find) == 0) {
        return false;
    }

    $isThis = substr($str, strlen($str) - strlen($find));
    if ($isThis == $find) {
        return true;
    }
    return false;
}

//	bool: checks whether string exists in line
function contains($str, $find)
{
    if (strlen($find) > strlen($str)) {
        return false;
    }

    if (strlen($find) == 0) {
        return false;
    }

    for ($i = 0; $i < strlen($str); $i++) {
        $isThis = substr($str, $i, strlen($find));
        if ($isThis == $find) {
            return true;
        }
    }

    return false;
}
?>