<?php

//IP Banned?
$IP_BANNED = array(
	'65.246.85.99', //Don't remember who this is
	'82.171.97.225', //Juffrow_Twiddel. He requested a temp ban for school concerns. (Unban on 1/20/11)
	'99.139.121.145', //Reddit spammer
	'99.224.244.160' //Called us fags. I don't like him
);
if(in_array(getIP(),$IP_BANNED)) die("Damn, you really pissed me off didn't you? Have a nice day elsewhere.");

//Keep it smooth
session_start(); 
ob_start();
date_default_timezone_set('America/New_York');


//What are we running?
define("NAME","CLICKQUEST");
define("VERSION","1");
define("MAJOR","1");
define("MINOR","1");
define("REVISION","6");
define("COPYRIGHT","Copyright 2010 Bionic Trousers / Exim Works");

//Make sure we have functionality
require_once('../config.php');
require_once("color.php");
require_once("user.php");

//Define $USER
if(!empty($_SESSION['hash'])) {
	$USER = unserialize(base64_decode($_SESSION['hash']));
} else {
	$USER = new User();
}

//Global Functions
function getIP() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

//Error Handling
define("DEBUG", true);
define("ADMIN", true);

function logDebug($var) { if(DEBUG) writeToFile("../debug.log", $var); }
function logAdmin($var) { if(ADMIN) writeToFile("../admin.log", $var); }
function logError($var) { writeToFile("../error.log", $var); }

function writeToFile($file, $var) {
	$var = "*** START LOG ***\n" . $var . "*** END LOG ***\n\n";
	try {
		file_put_contents($file, $var, FILE_APPEND); 
	} catch(Exception $e) {
		echo "Error @ writeToFile()!";
	}
}
?>