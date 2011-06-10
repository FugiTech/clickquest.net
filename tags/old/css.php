<?php
header("Content-type: text/css");

//Start the party
session_start();


//Make sure we have functionality
require("error.php");
require("color.php");
require("user.php");
require("func.php");

//Here Lassie...
try {
	$USER = unserialize(base64_decode($_SESSION['hash']));
//	echo '/','*', ' SUCCESS ', '*','/',"\n";
} catch(Exception $e) {
//	echo '/','*', $e, ' - ', $e->getMessage(), '*','/',"\n";
	$USER = False;
}

//Where did I fuck up?
//echo '/','*', "\n", $_SESSION['hash'], "\n", var_export($USER,True), "\n", '*','/',"\n";
?>

/*\
|*| The Over-Arching Container
\*/
html {
	height: 100%;
	width: 100%;
	margin: 0px;
	padding: 0px;
}
body {
	background: black;
	color: white;
	height: 100%;
	width: 100%;
	margin: 0px;
	padding: 0px;
//	overflow: hidden;

	font-family: "Courier", sans-serif;
	font-weight: bold;
}

/*\
|*| The Major Containers
\*/

#title {
	height: 5%;
	width: 100%;
	text-align: left;
	font-size: 2em;
}

#main {
	float: left;
	width: 69%;
	height: 95%;
}

#you {
	color: #<?php echo($USER ? $USER->getColor()->getHex() : 'white'); ?>;
	height: 70%;
	width: 100%;
}

#others {
	height: 30%;
}

#chat {
	float: left;
	width: 30%;
	height: 95%;
}

#welcome {
	font-size: 2em;
}

/*\
|*| Allows centering
\*/

.vertouter {
	display: table;
}

.vertinner {
	display: table-cell;
	vertical-align: middle;
	text-align: center;
	width: 100%;
}

/*\
|*| Progress
\*/

#name {
	font-size: 4.3em;
	line-height: 90%;
}

#level {
	font-size: 290px;
	line-height: 220px;
}

#total {
	font-size: 2em;
} 

#next {
	font-size: 2em;
}

.other {
	float: left;
	display: inline-block;
	width: 19.5%;
	height: 50%;
	line-height: 80%;
	text-align: center;
	font-size: 5.7em;
}

/*\
|*| Chat
\*/

#messages {
	height: 95%;
	width: 100%;
	overflow: auto;
}

.chatline {
	display: block;
}

#sender {
	position: relative;
	height: 5%;
	height: 30px;
	width: 100%
	margin: 0px;
	padding: 0px;
	border: 0px;
}

#mes {
	float: left;
	width: 82%;
	margin: 0px;
	padding: 0px;
	background: black;
	border: 1px solid gray;
	color: #<?php echo($USER ? $USER->getColor()->getHex() : 'white'); ?>;
}

#messend {
	float: left;
	width: 16%;
	margin: 0px;
	margin-left: 1%;
	padding: 0px;
	background: black;
	border: 1px solid gray;
	color: white;
}
/*\
|*| Other
\*/
.colorselector {
	border: 0px;
	height: 60px;
	width:  60px;
}