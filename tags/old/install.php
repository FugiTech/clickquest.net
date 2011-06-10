<?php
/*
$db = sqlite_factory('new/user.sql');
//$db->queryExec('DROP TABLE users');
$db->queryExec('CREATE TABLE users (
	id INTEGER PRIMARY KEY, 
	username TEXT NOT NULL UNIQUE, 
	password TEXT NOT NULL, 
	clicks INTEGER NOT NULL DEFAULT 0, 
	level INTEGER NOT NULL DEFAULT 0,
	color TEXT NOT NULL DEFAULT "FFFFFF",
	ip TEXT NOT NULL,
	time INTEGER NOT NULL DEFAULT 0,
	admin INTEGER NOT NULL DEFAULT 0,
	banned INTEGER NOT NULL DEFAULT 0
)');
//$db->queryExec('INSERT INTO users(username,password,ip,admin,clicks) VALUES("Fugiman","roflcopter","",1,13371337)');
//$db->queryExec('INSERT INTO users(username,password,ip,admin) VALUES("herpa_derp","alex","",1)');

*/
$db = sqlite_factory('chat.sql');
$db->queryExec('DROP TABLE chat');
$db->queryExec('CREATE TABLE chat (
	id INTEGER PRIMARY KEY, 
	name TEXT NOT NULL DEFAULT " > ", 
	message TEXT NOT NULL DEFAULT "", 
	color TEXT NOT NULL DEFAULT "FFFFFF",
	level INTEGER NOT NULL DEFAULT 0,
	time INTEGER NOT NULL DEFAULT 0,
	ip TEXT NOT NULL
)');
/*
IF NOT EXISTS 
 AUTOINCREMENT
*/
?>
