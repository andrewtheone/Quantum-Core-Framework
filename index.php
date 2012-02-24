<?php
error_reporting(E_ALL);
session_start();

function getmicro()
	{
		$d = explode(" ",microtime());
		return $d[1]+$d[0];
	}
$start = getmicro();

require_once "system/bootstrap.php";

new Bootstrap;
$end = getmicro();
echo (round($end-$start, 3)*1000)." ms";
?>
