<?php

session_start(); 
ob_start();

//What are we running?
define("TITLE","CLICKQUEST");
define("BUILD","2.942");
define("PATCH","1.221.3");
define("REVISION","16b");

//Make sure we have functionality
require("error.php");
require("color.php");
require("user.php");
require("func.php");

//Logged in?
if($_POST['submit']) {
	$USER = new User($_POST['username'], $_POST['password'], getIP());
	if(!$USER->getSuccess()) {
		echo $USER->getError()->toHTML();
		define("LOGIN",True);
	} else {
		define("LOGIN",False);
	}
} elseif(!empty($_SESSION['hash']) && $_SESSION['hash'] != "Tjs=") {
	$USER = unserialize(base64_decode($_SESSION['hash']));
	if(!$USER->getSuccess()) {
		echo $USER->getError()->toHTML();
		define("LOGIN",True);
	} else {
		define("LOGIN",False);
		if($USER->isUpdate()) $USER = $USER->getUpdate();
	}
} else {
		define("LOGIN",True);
}

//Do something first?
if(!empty($_POST['color']) && !LOGIN) {
	$USER->setColor($_POST['color']);
}

//Display page, increment clicks, post chat message or get chat
if(isset($_GET['c'])) {
	echo updateChat();
} elseif(!empty($_POST['message']) && !empty($_POST['security']) && !LOGIN) {
	verifyHash($_POST['security']);
	$USER->addChat($_POST['message']);
	echo updateChat();
} elseif(!empty($_POST['increment']) && !LOGIN) {
	verifyHash($_POST['increment']);
	$USER->incrementClicks();
	print number_format($USER->getLevel())."|".number_format($USER->getClicks())."|".number_format($USER->calcRemaining());
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title><?php echo title_string(); ?></title>
		<link href="css.php" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="js.php"></script>
	</head>
	<body>
	<div id="title"><?php echo title_string(); ?></div>
		<div id="main">
			<div id="you" class="vertouter">
				<div class="vertinner">
<?php if(LOGIN) { ?>
					<form method="post" action="index.php">
						<input type="text" id="username" name="username" value="Username" /><br />
						<input type="password" id="password" name="password" value="Password"/><br />
						<input type="submit" id="submit" name="submit" value="Login" />
						<input type="reset" id="reset" name="reset" value="Clear" />
					</form>
<?php } elseif($USER->getColor()->isDefault()) { ?>
					<form method="post" action="index.php">
					<span id="welcome">Welcome, <?php echo $USER->getName(); ?>. Please select your color.</span><br />
<?php
	foreach($USER->getColor()->colorArray('normal') as $name=>$hex) {
		echo "\t\t\t\t\t\t".'<button type="submit" name="color" class="colorselector" style="background: #'.$hex.';" value="'.$name.'"></button>'."\n";
	}
?>
					</form>
<?php } elseif($USER->getLevel() >= 50 && $USER->getColor()->getModifier() == 'normal') { ?>
					<form method="post" action="index.php">
					<span id="welcome">Congratulations, <?php echo $USER->getName(); ?>. Please select your new color.</span><br />
<?php
	foreach($USER->getColor()->colorArray($USER->getColor()->getName()) as $name=>$hex) {
		if($name=='normal') continue;
		echo "\t\t\t\t\t\t".'<button type="submit" name="color" class="colorselector" style="background: #'.$hex.';" value="'.$name.' '.$USER->getColor()->getName().'"></button>'."\n";
	}
?>
					</form>
<?php } else { ?>
					<span id="name"><?php echo $USER->getName(); ?></span>
					<div id="level">
						<span id="levelnum"><?php echo $USER->getLevel(); ?></span>
					</div>
					<div id="total">
						<span id="totalnum"><?php echo $USER->getClicks(); ?></span>
						<span> total clicks</span>
					</div>
					<div id="next">
						<span>next: </span>
						<span id="nextnum"><?php echo $USER->calcRemaining(); ?></span>
						<span> clicks</span>
					</div>
<?php } ?>
				</div>
			</div>
			<div id="others"><?php echo getOthers(); ?></div>
		</div>
		<div id="chat">
			<div id="messages"><?php echo getChat(); ?></div>
			<div id="sender">
				<input type="text" id="mes" />
				<input type="button" id="messend" value="Send" />
				<span id="reporter">CHAT BROKEN :(</span>
			</div>
		</div>
	</body>
</html>
<?php } $_SESSION['hash'] = base64_encode(serialize($USER)); ?>