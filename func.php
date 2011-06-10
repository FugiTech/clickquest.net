<?php

function title_string() {
	return TITLE.' BUILD '.BUILD.' patch '.PATCH.' rev '.REVISION;
}

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

function getOthers($num=null) {
	if(is_null($num)) $num=10;

	$return = "\n";

	$db = sqlite_factory('clickquest.sql');
	$result = $db->query('SELECT level, color FROM users ORDER BY RANDOM() LIMIT '.$num)->fetchAll(SQLITE_ASSOC);
	foreach($result as $key=>$value) {
		$color = new Color($value['color']);
		$return .= "\t\t\t\t";
		$return .= $color->format($value['level'],'other');
		$return .= "\n";
	}
	$return .= "\t\t\t";
	return $return;
}

function getChat() {
	$db = sqlite_factory('clickquest.sql');
	$return = "\n";
	$chat = $db->query("SELECT *  FROM chat ORDER BY id DESC LIMIT 100")->fetchAll();
	$_SESSION['chat'] = $chat[0]['id'];
	foreach(array_reverse($chat) as $chatline) {
		$color = new Color($chatline['color']);
		if(empty($chatline['name']) || trim($chatline['name']) == '>') {
			$name = $chatline['name'];
		} else {
			$name = $chatline['name'].'('.$chatline['level'].'): ';
		}
		$return .= "\t\t\t\t".$color->format($name.$chatline['message'],'chatline')."\n";
	}
	$return .= "\t\t\t";
	return $return;
}

function updateChat() {
	$db = sqlite_factory('clickquest.sql');
	$return = "";
	$chat = $db->query("SELECT *  FROM chat WHERE id > ".($_SESSION['chat'] ? $_SESSION['chat'] : '0')." ORDER BY id DESC ")->fetchAll();
	$_SESSION['chat'] = ($chat[0]['id'] ? $chat[0]['id'] : $_SESSION['chat']);
	foreach(array_reverse($chat) as $chatline) {
		$color = new Color($chatline['color']);
		if(empty($chatline['name']) || trim($chatline['name']) == '>') {
			$name = $chatline['name'];
		} else {
			$name = $chatline['name'].'('.$chatline['level'].'): ';
		}
		$return .= "\t".$color->format($name.$chatline['message'],'chatline')."\n\t\t\t";
	}
	return $return;
}

function verifyHash($secure) {
	$time = "";
	$hash = "";
	$counter = 0;
	foreach(str_split($secure,3) as $chunk) {
		if($counter > 12) {
			$hash .= $chunk;
			continue;
		}
		$time .= substr($chunk,-1);
		$hash .= substr($chunk,0, 2);
		$counter++;
	}
	$timehash = md5("Be".sha1("Very".md5("Careful".$time)));
	if($hash != $timehash) {
		$debug = "User '".$USER->getName()."' attempted invalid click. Secure hash='".$_POST['increment']."'; Time='".$time."'; Hash='".$hash."'; Time-Hash='".$timehash."'";
		//print $debug;
		error_log($debug,3,"error.log");
		die(-1);
	}
}

?>