<?php


/***************************************************************
 * define stages here
 *************************************************************/

require('wp-stages.php');

/******** Load appropriate configuration depending on the stage ********/

$host = $_SERVER['HTTP_HOST'];

if(array_key_exists($host, $stage))
{
	$current_stage = $stage[$host];
	define("CURRENT_STAGE", $current_stage);
	require_once("wp-config-{$current_stage}.php");
}
else
{
	die("We are currently updating our site.");
}
