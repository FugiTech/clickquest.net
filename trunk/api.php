<?php
require('include/core.php');

/*
 * 	Log In functions
 */
if(isset($_GET['isLoggedIn'])) {
		echo json_encode(array(
			"loggedin" => $USER->isIn()
		)/* , JSON_FORCE_OBJECT */);
} elseif(isset($_GET['isUser'])) {
		echo json_encode(array(
			"exists" => User::isUser($_POST['username'])
		)/* , JSON_FORCE_OBJECT */);
} elseif(isset($_GET['logIn'])) {
		echo json_encode($USER->login($_POST['username'], $_POST['password'])/* , JSON_FORCE_OBJECT */);
} elseif(isset($_GET['loadUser'])) {
		echo json_encode(array(
			"start" => $USER->getStart(),
			"totalTime" => $USER->getTotalTime(),
			"level" => $USER->getLevel(),
			"clicks" => $USER->getTotalClicks(),
			"ip" => $USER->getIP(),
			"color" => $USER->getColor(),
			"user" => $USER->getName(),
			"action" => $USER->getAction()
		)/* , JSON_FORCE_OBJECT */);
		
		
/*
 * Sync Functions
 */
} elseif(isset($_GET['heartbeat'])) {
		$USER->updateTime($_POST['time']);
		echo $USER->proccessHeartbeat($_POST['action']);
} elseif(isset($_GET['calcTotal'])) {
		echo json_encode(array(
			"clicks" => User::calcTotal($_POST['level'])
		)/* , JSON_FORCE_OBJECT */);
		
		
/*
 * Getter Functions
 */
} elseif(isset($_GET['getChat'])) {
		echo json_encode(array(
			"chat" => User::getChat()
		)/* , JSON_FORCE_OBJECT */);
} elseif(isset($_GET['getGeneral'])) {
		echo json_encode(array(
			"stats" => $USER->getStats()
		)/* , JSON_FORCE_OBJECT */);
} elseif(isset($_GET['getChatLog'])) {
		echo json_encode(
			$USER::getChatPage((int)$_POST['pgid'])
		/* , JSON_FORCE_OBJECT */);		
		
		
/*
 * Setter Functions
 */
} elseif(isset($_GET['addChat'])) {
		echo json_encode(array(
			"success" => $USER->addChat($_POST['message'])
		)/* , JSON_FORCE_OBJECT */);
} elseif(isset($_GET['setColor'])) {
		echo json_encode(array(
			"success" => $USER->setColor(strtoupper($_POST['hex']))
		)/* , JSON_FORCE_OBJECT */);
		
		
/*
 *  Log Out Functions
 */
} elseif(isset($_GET['logOut'])) {
		echo json_encode(array(
			"success" => $USER->logout()
		)/* , JSON_FORCE_OBJECT */);
}

$_SESSION['hash'] = base64_encode(serialize($USER));

/*

//Logged in?
if($_POST['submit']) {
	$USER = new User();
	if($USER->exists($_POST['username'])) {
		$USER->login($_POST['username'], $_POST['password']);
	}
	if(!$USER->getSuccess()) {
		echo $USER->getError()->toHTML();
		define("LOGIN",True);
	} else {
		define("LOGIN",False);
	}
} else

//Logout
if(isset($_GET['logout'])) {
	$USER->logout();
	unset($_SESSION['hash']);
	define("LOGOUT",True);
} else {
	define("LOGOUT",False);
}

//Maintenence?
if(False && !$USER->getAdmin()) { //<--Set that to True if Maintence, or False if not
	echo 'Hey, doing work. Clickquest will be back shortly. Thank you for your patience!';
	die();
}

*/ ?>