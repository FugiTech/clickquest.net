<?php
require_once("../config.php");

class User
{
	const JSON_FALSE = '{"success":false}';
	const CPS_MIN = -1; //Minimum number of CPS to require validation.  Changed from 2 to -1 by Invariel.  A 1 CPS bot is still botting.
	const CPS_LIMIT = 12; //Maximum CPS allowed
	const CPS_SIZE = 20; //Minimum number of data points to allow advanced verification
	
	const initial_clicks = 0;
	const first_level_clicks = 100;
	const modifier = 1.09;
	const hcoremod = 0.02;
	const hcoreadd = 100;
	
	//Basic info
	private $id = -1;
	private $name = "";
	
	//Progress
	private $clicks = -1;
	private $modified = -1;
	private $level = -1;
	private $ip = '-1.-1.-1.-1';
	private $color;
	private $action = array();
	
	//Database checks
	private $stored_clicks = -1;
	private $stored_modified = -1;
	private $stored_ip = '-1.-1.-1.-1';
	private $stored_color = 'GGGGGG';
	
	//Trackers
	private $start = -1; //Begining of the session
	private $total = -1; //Total time spent on CQ
	private $last = -1; //Last heartbeat
	private $activity = -1; //Last activity (chat or click)
	private $sync = -1; //Last sync with the database
	private $CPS = array(); //CPS history for last 100 heartbeats
	
	//Flags
	private $admin = False;
	private $mod = False;
	private $banned = False;
	private $online = -1;
	private $failCount = 0;
	private $hardcore = False;
	
	//Handles
	private $db;
	private $fatal=False;
	private $logged_in=False;
	private $idle = False;
	
	//Universal Truthes
	function __construct() {
		$this->db = self::connect();
		$this->color = new Color();
	}
	function login($username,$password) {
		$id = self::arrayQuery('SELECT id FROM users WHERE LOWER(username)="'.$this->db->real_escape_string(strtolower($username)).'" LIMIT 1','id');
		if($id === False) {
			if(strlen($username) > 16) return array("success"=>False, "error"=>"Username too long, must be 16 or less characters");
			if(!preg_match('/^[A-Za-z0-9_]*$/',$username)) return array("success"=>False, "error"=>"Username contains invalid characters. Use only alphanumeric or underscore characters.");
			$this->db->query('INSERT INTO users(username,password,clicks) VALUES("'.$this->db->real_escape_string($username).'","'.$this->db->real_escape_string(md5(sha1($password))).'","'.(self::initial_clicks).'")');
			$id = $this->db->insert_id;
		}
		$temp = self::arrayQuery('SELECT * FROM users WHERE id="'.$id.'" LIMIT 1');
		if($temp['password'] != md5(sha1($password))) return array("success"=>False, "error"=>"Invalid Password");
		
		$this->id		= $temp['id'];
		$this->name		= $temp['username'];
		$this->clicks	= $temp['clicks'];
		$this->modified	= $temp['modified'];
		$this->level	= $temp['level']; //Not actually used anywhere...
		$this->ip		= getIP();
		$this->color	= new Color($temp['color']);
		$this->stored_clicks	= $temp['clicks'];
		$this->stored_modified	= $temp['modified'];
		$this->stored_level		= $temp['level'];
		$this->stored_ip		= $temp['ip'];
		$this->start	= time();
		$this->admin	= $temp['admin'];
		$this->mod		= $temp['mod'];
		$this->LRR		= $temp['lrr'];
		$this->banned	= $temp['banned'];
		$this->hardcore	= $temp['hardcore'];
		$this->online	= $temp['online'];
		$this->total	= $temp['totaltime'];
		$this->logged_in= 1;
		$this->failCount= $temp['fail'];
		
		$this->activity = time();
		$this->setLevel($this->calcLevel($this->getClicks()));
		
		if($this->banned) { $this->logout(); return array("success"=>True, "error"=>"User is Banned"); }
		if($this->ip != $temp['ip']) { $this->db->query('UPDATE users SET ip="'.$this->db->real_escape_string($this->ip).'" WHERE id="'.($this->id).'"'); }
		
		return array("success"=>True, "error"=>"None");
	}
	function logout() {
		$this->db->query("UPDATE users SET totaltime=".$this->total." WHERE id=".$this->id);
		$this->id = -1;
		$this->name = "";
		$this->clicks = -1;
		$this->modified = -1;
		$this->level = -1;
		$this->ip = '-1.-1.-1.-1';
		$this->color = new Color();
		$this->action = array();
		$this->stored_clicks = -1;
		$this->stored_modified = -1;
		$this->stored_ip = '-1.-1.-1.-1';
		$this->stored_color = 'GGGGGG';
		$this->start = -1; //Begining of the session
		$this->total = -1;
		$this->last = -1; //Last heartbeat
		$this->activity = -1; //Last activity (chat or click)
		$this->sync = -1; //Last sync with the database	
		$this->CPS = array();
		$this->admin = False;
		$this->mod = False;
		$this->LRR = False;
		$this->banned = False;
		$this->hardcore = False;
		$this->online = -1;
		$this->db = null;
		$this->fatal=False;
		$this->logged_in=False;
		$this->failCount = 0;
		return True;
	}
	function banHammer() {
		if(!$this->db->query('UPDATE users SET banned=1 WHERE id='.($this->id)))
			logError($this->db->error);
		$this->logout();
	}

	//Specific Getter Functions
	function getName() { return $this->name; }
	function getClicks() { return $this->clicks+$this->modified; }
	function getLevel() { return $this->level; }
	function getIP() { return $this->ip; }
	function getColor() { return $this->color->getHex(); }
	function getAction() { if (isset($this->action[0])){return $this->action[0];} else {return null;}}
	function getStart() { return $this->start; }
	function getTotalTime() { return $this->total; }
	function isAdmin() { return ($this->admin ? True : False); }
	function isMod() { return ($this->mod ? True : False); }
	function isLRR() { return ($this->LRR ? True : False); }
	function isBanned() { return ($this->banned ? True : False); }
	function isHardcore() { return ($this->hardcore ? True : False); }
	function isIn() { return ($this->logged_in ? True : False); }
	
	//General Getter Functions
	static function getOnline() {
		$db = self::connect();
		$result = $db->query('SELECT level, color, username, hardcore FROM users WHERE banned=0 AND online>'.(time()-120).' ORDER BY id ASC');
				if($result===False) return $db->error;
		$return = '<div class="holder"><div class="row1">';
		$key = 0;
		while($value = $result->fetch_assoc()) {
			$return .= '<div class="other" style="color: #'.$value['color'].';"><span class="name">'.($value['hardcore'] ? '[H]' : '').$value['username'].'</span><br /><span class="level">'.$value['level'].'</span></div>';
			if(($key+6) % 10 == 0) $return .= '</div><div class="row2">';
			if(($key+1) % 10 == 0) $return .= '</div></div><div class="holder"><div class="row1">';
			$key++;
		}
		$return .= '</div></div>';
		$result->close();
		return array("html"=>$return,"number"=>$key);
	}
	static function getChat() { 
		$_SPECIAL = array(1);
		$db = self::connect();
		$return = "";
		$result= $db->query("SELECT * FROM chat ORDER BY id DESC LIMIT 100");
		if($result===False) return $db->error;
		while($chatline = $result->fetch_assoc()) {
			if(empty($chatline['name']) || trim($chatline['name']) == '>') {
				$name = $chatline['name'];
			} else {
				$name = date('H:i:s ',$chatline['time']).$chatline['name'].'['.$chatline['level'].']: ';
			}
			$return = "<span class='chatline' style='color: #".($chatline['level'] > 99 ? '000000; text-shadow: #'.$chatline['color'].' 0px 0px 3px; text-shadow: #'.$chatline['color'].' 0px 0px 3px; text-shadow: #'.$chatline['color'].' 0px 0px 3px' : $chatline['color']).";'>".$name.$chatline['message']."</span>".$return;
		}
		$result->close();
		$result= $db->query("SELECT * FROM chat WHERE id < 0 ORDER BY id DESC LIMIT 100");
		if($result===False) return $db->error;
		while($chatline = $result->fetch_assoc()) {
			$name = date('H:i:s ',$chatline['time']).$chatline['name'].' ';
			$return .= "<span class='chatline' style='color: #".$chatline['color'].";'>".$name.$chatline['message']."</span>";
		}
		$result->close();
		return $return;
	}
	static function getChatPage($pid,$results){
		$db = self::connect();
		$return='';
		$r= $db->query('SELECT COUNT(id) FROM chat');
		$lines=$r->fetch_row();
		$r->close();
		$lines=(int) $lines[0];
		if($results<1 || !is_int($results)){
			$results=20;
		}
		$pages=ceil($lines/$results);
		if($pid<1 || !is_int($pid)){
			$pid=1;
		} elseif($pid>$pages){
			$pid=$pages;
		}
		$start=($pid-1)*$results;
		$result= $db->query("SELECT * FROM chat ORDER BY id ASC LIMIT $start , $results");
		if($result===False) return $db->error;
		while($chatline = $result->fetch_assoc()) {
			if(empty($chatline['name']) || trim($chatline['name']) == '>') {
				$name = $chatline['name'];
			} else {
				$name = date('H:i:s ',$chatline['time']).$chatline['name'].'['.$chatline['level'].']: ';
			}
			$return = $return."<span class='chatline' style='color: #".($chatline['level'] > 99 ? '000000; text-shadow: #FFFFFF 0px 0px 3px' : $chatline['color']).";'>".$name.$chatline['message']."</span>";
		}
		$result->close();
		return array("log"=>$return, "pid"=>$pid, "pages"=>$pages, "lines"=>$lines, "results"=>$results);
	}
	function getStats($offset=2) {
		$result	= self::arrayQuery('SELECT count(id) as user, sum(clicks+modified)+(sum(hardcore)*6666666) as total, sum(level) as level, sum(fail) as fail FROM users WHERE banned=0');
		$r = $this->db->query("CREATE TEMPORARY TABLE leaderboard ( `rank` INT NOT NULL AUTO_INCREMENT, `username` VARCHAR( 16 ) NOT NULL, `level` INT NOT NULL DEFAULT '0', `clicks` INT NOT NULL DEFAULT '0', `color` CHAR(6) NOT NULL, `lrr` TINYINT NOT NULL DEFAULT '0',`fail` INT NOT NULL DEFAULT '0', PRIMARY KEY ( `rank` )) ENGINE=MEMORY;");
		if($r === False)
			logDebug("FAILED TO CREATE TABLE");
		$r = $this->db->query("INSERT INTO leaderboard (username, level, clicks, color, lrr, fail) SELECT username, level, (clicks+modified), color, lrr, fail FROM users WHERE banned = 0 AND hardcore=0 ORDER BY (clicks+modified) DESC;");
		if($r === False)
			logDebug("FAILED TO POPULATE TABLE");
		$rank = (int)self::arrayQuery("SELECT rank FROM leaderboard WHERE username='".$this->name."';",'rank',$this->db);
		$big = self::fullQuery("SELECT rank, username, clicks, level, color, fail FROM leaderboard WHERE rank <= 10 OR lrr = 1 OR (".($rank-$offset)." <= rank AND rank <= ".($rank+$offset).");",$this->db);
		$r = $this->db->query("DROP TABLE leaderboard");
		if($r === False)
			logDebug("FAILED TO RELEASE TABLE");
		$stat = array(
			'user' => number_format($result['user']),
			'total' => number_format($result['total']),
			'fails' => number_format($result['fail']*-1),
			'avg' => number_format($result['total']/$result['user'],2),
			'level' => number_format($result['level']/$result['user'],2),
			'fail' => number_format(($result['fail']*-1)/$result['user'],2)
		);
		$return = "<p>Users: ".$stat['user']."<br />\n".
					"Total Clicks: ".$stat['total']."<br />\n".
					"Total Fails: ".$stat['fails']."<br />\n".
					"Average Clicks: ".$stat['avg']."<br />\n".
					"Average Level: ".$stat['level']."<br />\n".
					"Average Fail: ".$stat['fail']."<br />\n<br />\n";
	foreach($big as $row){
		$return .= '<span style="color: #'.$row['color'].';">';
		$return .= '#'.$row['rank'].': ';
		$return .= $row['username'].' at '.number_format($row['clicks']).' clicks [Level '.$row['level'].']&lt;'.($row['fail']*-1).' Fails&gt;';
		$return .= '</span><br />'."\n";
	}
		$return .= <<<STATS
<table>
	<tr>
		<td>Color</td>
		<td># of Players</td>
		<td>Highest Clicks</td>
		<td>Total Clicks</td>
		<td>Clicks Per User</td>
	</tr>
STATS;
	foreach(Color::colorArray() as $key=>$val) {
		$result = $this->db->query('SELECT 
			count(id) as user, 
			sum(clicks+modified) as total, 
			max(clicks+modified) as max
			FROM users
			WHERE 
			banned=0 AND
			hardcore=0 AND
			(
				color="'.$val['normal'].'" OR
				color="'.$val['light'].'" OR
				color="'.$val['dark'].'"
			)')->fetch_assoc();
		$return .= '<tr style="color: black; background: #'.$val['normal'].';">';
		$return .= '<td>'.$key.'</td>';
		$return .= '<td>'.$result['user'].'</td>';
		$return .= '<td>'.number_format($result['max']).'</td>';
		$return .= '<td>'.number_format($result['total']).'</td>';
		$return .= '<td>'.number_format($result['total']/$result['user']).'</td>';
		$return .= '</tr>';
	}
	$return .= "</table>\n";
	
	//Hall of Fame
	$big = self::fullQuery("SELECT username, clicks+modified as total, level, color, fail FROM users WHERE hardcore=1 AND banned=0;",$this->db);
	if($big!==False) {
		$return .= '<br /><h2>Hall of Fame</h2><h3>For all the players who defeated Level 100 and kept going</h3><br />';
		foreach($big as $row){
			$return .= '<span style="color: #'.$row['color'].';">';
			$return .= $row['username'].' at '.number_format($row['total']).' clicks [Level '.$row['level'].']&lt;'.($row['fail']*-1).' Fails&gt;';
			$return .= '</span><br />'."\n";
		}
	}
	$return .= "<br />Generated at: ".date(DATE_RSS)."</p>";
	
	return $return;
}
	
	
	function checkIdle() { $this->idle = ($this->activity > time()-120 ? True : False); }
	private function buildAction() {
		$this->action = array();
		//Level 100? Flip a bitch!
		if($this->level >= 100) {
			$str = "STOP CLICKING!!";
			$str .= "<br /><span class='sub'>";
			if($this->getClicks() < 6500000) {
				$str .= "If you keep clicking, bad things will happen!";
			} elseif ($this->getClicks() < 6525000) {
				$str .= "I am not joking. Stop clicking NOW or you will regret it!";
			} elseif ($this->getClicks() < 6550000) {
				$str .= "Seriously? What do I have to say to get you to stop?";
			} elseif ($this->getClicks() < 6575000) {
				$str .= "You know what? I don't care. Keep clicking!";
			} elseif ($this->getClicks() < 6600000) {
				$str .= "Reverse psychology didn't work? Then how about this. I will kill your pets/family/friends if you don't stop.";
			} elseif ($this->getClicks() < 6610000) {
				$str .= "Fine, I'll tell you. If you hit 6,666,666 clicks, you get removed from the leaderboard.";
			} elseif ($this->getClicks() < 6620000) {
				$str .= "NOBODY WILL KNOW YOU EXIST.";
			} elseif ($this->getClicks() < 6630000) {
				$str .= "You caught me. You also get on the Hall of Fame.";
			} elseif ($this->getClicks() < 6640000) {
				$str .= "That isn't really a good thing though. Its kind of hidden away.";
			} elseif ($this->getClicks() < 6650000) {
				$str .= "As in 'bottom of the stats page where nobody looks' hidden away.";
			} elseif ($this->getClicks() < 6660000) {
				$str .= "That isn't all though...";
			} elseif ($this->getClicks() < 6666000) {
				$str .= "You know how mean I am. There is always something bad.";
			} elseif ($this->getClicks() < 6666600) {
				$str .= "And between you and me, this is my best troll to date!";
			} else {
				$str .= "HAHAHA YOU FOOL. YOU'RE GOING TO RESET YOUR CLICKS! CAUGHT YOU!";
			}
			$str .= "</span>";
			array_push($this->action,$str);
		}
		//Initial Color Selection
		if($this->level < 75 && $this->color->isDefault()) {
			$str = "Select A Color:<br /><span class='sub'>";
			foreach(Color::colorArray('normal') as $name=>$hex) {
				$str .= "<a class='colorselector' style='color:#".$hex.";'>".$name."</a> ";
			}
			$str .= "</span>";
			array_push($this->action,$str);
		}
		//Level 50 shade selection
		if($this->level < 75 && $this->level >= 50 && $this->color->getModifier()=='normal') {
			$str = "Select A Shade:<br /><span class='sub'>";
			foreach(Color::colorArray($this->color->getName()) as $name=>$hex) {
				if($name=='normal') continue;
				$str .= "<a class='colorselector' style='color:#".$hex.";'>".$name."</a> ";
			}
			$str .= "</span>";
			array_push($this->action,$str);
		}

		//Lulz
		if($this->hardcore) {
			$str = "DON'T PANIC";
			array_push($this->action,$str);
		}
	}
	//Setter Functions
	function updateTime($time) {
		$this->total = (int)$time;
	}
	function addChat($mes) {
		if(!$this->logged_in) return False;
		$this->doubleCheck();
		if($this->banned == 1) return False;
			$idle = $this->idle;
			$this->activity = time();
			$this->checkIdle();
			if($idle != $this->idle) $this->resync();
		if($this->admin) {
			$name = '&lt;ADMIN&gt;'.$this->name;
		} elseif($this->mod) {
			$name = '&lt;GM&gt;'.$this->name;
		} elseif($this->LRR) {
			$name = '&lt;LRR&gt;'.$this->name;
		} else {
			$name = $this->name;
		}
		if($this->hardcore)
			$name = '[H]'.$name;
		return $this->db->query("INSERT INTO chat(userid, name, message, color, level, ip, time) VALUES ('".$this->id."', '".$name."', '".$this->db->real_escape_string(preg_replace('(\bhttp://[^ ]+\b)', '<a href="$0" target="_blank">$0</a>', ($this->admin ? $mes : htmlspecialchars($mes))))."', '".$this->color->getHex()."', '".$this->level."', '".$this->ip."', '".time()."')");
	}
	function setColor($code) {
		$temp = new Color($code);
		if(
			($this->color->isDefault() && $temp->getModifier()=='normal') || 
			($this->level >= 50 && $this->color->getModifier()=='normal' && $this->color->getName() == $temp->getName()) ||
			$this->level >= 75
		) {
			$this->color->setColor($code);
			$this->db->query("UPDATE users SET color='".$code."' WHERE id=".$this->id);
			return True;
		}
		return False;
	}
	private function setClicks($clicks) {
		$this->clicks = $clicks;
		if($this->getClicks() >= 6666666) {
			$this->hardcore = true;
			$this->clicks = -$this->modified;
			$this->resync();
		}
		$this->setLevel($this->calcLevel($this->getClicks()));
	}
	private function setLevel($level) {
		$old = $this->level;
		$this->level = $level;
	}
	private function calcLevel($clicks) {
		for($level=0; $clicks >= $this->calcTotal($level,false,$this->hardcore); $level++);
		return $level;
	}
		
	//Database Methods
	private function doubleCheck() {
		$temp = self::arrayQuery('SELECT * FROM users WHERE id="'.($this->id).'" LIMIT 1');
		$this->banned = $temp['banned'];
		$this->admin = $temp['admin'];
		$this->mod = $temp['mod'];
		$this->LRR = $temp['lrr'];
		if($this->color->getHex() != trim($temp['color']))
			$this->color = new Color($temp['color']);
	//	if($temp['clicks'] != $this->stored_clicks) {
	//		$this->clicks = ($this->clicks - $this->stored_clicks) + $temp['clicks'];
	//		$this->setLevel($this->calcLevel($this->clicks));
	//		$this->db->query("UPDATE users SET clicks=".$this->clicks.", level=".$this->level." WHERE id=".$this->id);
	//	}
	}
	private function resync() {
		if(!$this->db->query('UPDATE users SET clicks='.$this->clicks.', hardcore='.$this->hardcore.', level='.$this->level.', online='.$this->activity.' WHERE id='.($this->id)))
			logError($this->db->error);
		$this->sync = time();
	}

	function initializeUser() {
		$this->setClicks(self::initial_clicks);
		$this->resync();
	}
	
	//Heartbeat Functions
	function proccessHeartbeat($hash) {
		if(!$this->logged_in) return self::JSON_FALSE;
		$arg = $this->decodeHeartbeat($hash);
		if($arg !== False) {
			if($arg['time'] > $this->prev) {
				if($this->verifyHeartbeat($arg)) {
					$this->setClicks($this->clicks + $arg['newClicks']);
					if($arg['newClicks']>0) $this->activity = time();
					$idle = $this->idle;
					$this->prev = $arg['time'];
					$this->checkIdle();
					$superidle = ($this->activity < time()-600);
					if(time()-60 >= $this->sync || $idle != $this->idle || $superidle) $this->resync();
					if($superidle) { $this->logout(); return self::JSON_FALSE; }
				} else {
					$this->failCount--;
					if($this->failCount > 20) $this->banHammer();
					if(!$this->db->query('UPDATE users SET fail='.$this->failCount.' WHERE id='.($this->id)))
						logError($this->db->error);
					$this->logout();
					return self::JSON_FALSE;
				}
			}
			return $this->buildResponse();
		} else {
			return self::JSON_FALSE;
			$this->failCount++;
			if($this->failCount > 20) $this->banHammer();
			if(!$this->db->query('UPDATE users SET fail='.$this->failCount.' WHERE id='.($this->id)))
				logError($this->db->error);
			$this->logout();
			return self::JSON_FALSE;
		}
	}
	private function decodeHeartbeat($send) {
		$recieve = base64_decode($send);
		$parts = array("","","","");
		for($i=0;$i < strlen($recieve); $i = $i+4) {
			$parts[0] .= substr($recieve,$i+0,1);
			$parts[1] .= substr($recieve,$i+1,1);
			$parts[2] .= substr($recieve,$i+2,1);
			$parts[3] .= substr($recieve,$i+3,1);
		}
		foreach($parts as $key=>$val) {
			$parts[$key] = str_split(base64_decode(trim($val)),40);
			foreach($parts[$key] as $key2=>$val2) {
				$parts[$key][$key2] = trim($val2);
			}
		}
		if(
			$parts[1][1] == $parts[2][1] &&
			$parts[1][2] == $parts[3][1] &&
			$parts[0][1] == $parts[2][2] &&
			$parts[0][2] == $parts[3][2] &&
			$parts[1][1] ==  md5($parts[0][0].$parts[2][3].$parts[1][0]) &&
			$parts[1][2] == sha1($parts[1][3].$parts[3][3].$parts[3][0]) &&
			$parts[0][1] == sha1($parts[2][0].$parts[1][1].$parts[1][3]) &&
			$parts[0][2] ==  md5($parts[1][0].$parts[1][2].$parts[0][3])
		) {
			$start			= $parts[2][0];
			$time			= $parts[0][0];
			$level			= $parts[2][3];
			$newClicks		= $parts[1][0];
			$storedClicks	= $parts[1][3];
			$ip				= $parts[3][3];
			$color			= $parts[3][0];
			$name			= $parts[0][3];
		} else {
			$error .= "Verification Failed\n";
			$error .= date('r')."\n";
			$error .= $this->toString()."\n";
			$error .= "===================\n";
			$error .= "Hash1 verify ".( $parts[1][1] == $parts[2][1] ? "Pass" : "Fail - ".$parts[1][1]." vs ".$parts[2][1])."\n";
			$error .= "Hash2 verify ".( $parts[1][2] == $parts[3][1] ? "Pass" : "Fail - ".$parts[1][2]." vs ".$parts[3][1])."\n";
			$error .= "Hash3 verify ".( $parts[0][1] == $parts[2][2] ? "Pass" : "Fail - ".$parts[0][1]." vs ".$parts[2][2])."\n";
			$error .= "Hash4 verify ".( $parts[0][2] == $parts[3][2] ? "Pass" : "Fail - ".$parts[0][2]." vs ".$parts[3][2])."\n";
			$error .= "\n";
			$error .= "Hash1 Reconstruct ".( $parts[1][1] ==  md5($parts[0][0].$parts[2][3].$parts[1][0]) ? "Pass" : "Fail - ".$parts[1][1]." vs ".md5($parts[0][0].$parts[2][3].$parts[1][0]))."\n";
			$error .= "Hash2 Reconstruct ".( $parts[1][2] == sha1($parts[1][3].$parts[3][3].$parts[3][0]) ? "Pass" : "Fail - ".$parts[1][2]." vs ".sha1($parts[1][3].$parts[3][3].$parts[3][0]))."\n";
			$error .= "Hash3 Reconstruct ".( $parts[0][1] == sha1($parts[2][0].$parts[1][1].$parts[1][3]) ? "Pass" : "Fail - ".$parts[0][1]." vs ".sha1($parts[2][0].$parts[1][1].$parts[1][3]))."\n";
			$error .= "Hash4 Reconstruct ".( $parts[0][2] ==  md5($parts[1][0].$parts[1][2].$parts[0][3]) ? "Pass" : "Fail - ".$parts[0][2]." vs ".md5($parts[1][0].$parts[1][2].$parts[0][3]))."\n";
			logError($error);
			return False;
		}
		$combined = array(
			"start"=>$start,
			"time"=>$time,
			"level"=>$level,
			"newClicks"=>$newClicks,
			"storedClicks"=>$storedClicks,
			"ip"=>$ip,
			"color"=>$color,
			"name"=>$name
		);
		return $combined;
	}
	private function verifyHeartbeat($values) {
		$CPS = intval($values['newClicks']/($values['time']-$this->prev));
		array_unshift($this->CPS,$CPS);
		if(count($this->CPS) > 100) array_pop($this->CPS);
		if(!$this->verifyCPS($this->CPS)) return False;
		if(
			$values['start'] == $this->start &&
	//		(time()-5 <= $values['time'] && time() >= $values['time']) &&
	//		$this->prev + 4 <= $values['time'] &&
			$values['level'] == $this->calcLevel($values['newClicks'] + $values['storedClicks']) &&
	//		$values['newClicks'] <= 60 &&
			$values['storedClicks'] == ($this->getClicks()) &&
			$values['ip'] == $this->ip &&
			$values['color'] == $this->color->getHex() &&
			$values['name'] == $this->name
		) {
			return True;
		} else {
			$error .= "Verification Failed\n";
			$error .= date('r')."\n";
			$error .= $this->toString()."\n";
			$error .= "===================\n";
			$error .= "Start Time Match ".( $values['start'] == $this->start ? "Pass" : "Fail - ".$values['start']."(Client) vs ".$this->start."(Server)")."\n";
	//		$error .= "Time Comparison ".( time()-5 <= $values['time'] && time() >= $values['time'] ? "Pass" : "Fail - ".time()."(Now) vs ".$values['time']."(Sent)")."\n";
	//		$error .= "Ample Wait Check ".( $this->prev + 4 <= $values['time'] ? "Pass" : "Fail - ".$this->prev."(Prev) vs ".$values['time']."(Sent)")."\n";
			$error .= "Level Check ".( $values['level'] == $this->calcLevel($values['newClicks'] + $values['storedClicks']) ? "Pass" : "Fail - ".$values['level']."(Sent) vs ".$this->calcLevel($values['newClicks'] + $values['storedClicks'])."(Calc'd)")."\n";
	//		$error .= "Over-Click Check ".( $values['newClicks'] <= 60? "Pass" : "Fail - ".$values['newClicks']."(Sent) vs 60(Max)")."\n";
			$error .= "Stored Click Check ".( $values['storedClicks'] == $this->clicks ? "Pass" : "Fail - ".$values['storedClicks']."(Sent) vs ".$this->clicks."(Stored)")."\n";
			$error .= "IP Check ".( $values['ip'] == $this->ip ? "Pass" : "Fail - ".$values['ip']."(Sent) vs ".$this->ip."(Stored)")."\n";
			$error .= "Color Check ".( $values['color'] == $this->color->getHex() ? "Pass" : "Fail - ".$values['color']."(Sent) vs ".$this->color->getHex()."(Stored)")."\n";
			$error .= "Name Check ".( $values['name'] == $this->name ? "Pass" : "Fail - ".$values['name']."(Sent) vs ".$this->name."(Stored)")."\n";
			
			logError($error);
			return False;
		}
	}
	private function buildResponse() {
		$online = $this->getOnline();
		$this->buildAction();
		return json_encode(array(
			"online" => $online['html'],
			"number" => $online['number'],
			"chat" => $this->getChat(),
			"clicks" => ($this->getClicks()),
			"success" => True,
			"action" => $this->getAction()
		)/* , JSON_FORCE_OBJECT */);
	}
	//EXPAND EXPAND EXPAND
	private function verifyCPS($CPS) {
		$CPS = $this->clean($CPS);
		if(max($CPS) < self::CPS_MIN) return True; //1 CPS is not botting. Period.
		if(max($CPS) > self::CPS_LIMIT) {
			$error .= "Verification Failed\n";
			$error .= date('r')."\n";
			$error .= $this->toString()."\n";
			$error .= "===================\n";
			$error .= "Max over limit. Limit=".self::CPS_LIMIT.", calculated=".max($CPS);
			logAdmin($error);
			return False;
		} //Hard-Cap on clicking
		if(count($CPS) < self::CPS_SIZE) return True; //Prevent running calculations on small data sets
		if(0.9*max($CPS) < array_sum($CPS)/count($CPS)) {
			$error .= "Verification Failed\n";
			$error .= date('r')."\n";
			$error .= $this->toString()."\n";
			$error .= "===================\n";
			$error .= "Too concentrated. Off of max=".(0.9*max($CPS)).", off of array=".(array_sum($CPS)/count($CPS));
			logAdmin($error);
			return False;
		} //Are you clicking at your max all the time?
		$blarg = array_search(max(array_count_values($CPS)),array_count_values($CPS),true);
		if(max($CPS) > self::CPS_LIMIT * 7 / 10 && $blarg >= max($CPS)) { // Regularly clicking faster than 70% of MAX CPS
			$error .= "Verification Failed\n";
			$error .= date('r')."\n";
			$error .= $this->toString()."\n";
			$error .= "===================\n";
			$error .= "Too frequently high. Off of max=".(max($CPS)-1).", off of array=".$blarg;
			logAdmin($error); 
			return False;
		} //Is your most frequent clicking at the top of the spectrum?
		return True;
	}
	private function clean($CPS) {
		$new = array();
		foreach($CPS as $key=>$val) {
			$new[$key] = intval($val);
		}
		return $new;
	}
	
	//Static Methods
	static function isUser($username) {
		$id = self::arrayQuery('SELECT id FROM users WHERE LOWER(username)="'.self::connect()->real_escape_string(strtolower($username)).'" LIMIT 1','id');
		if($id===False) {
			return False;
		} else {
			return True;
		}
	}
	//Main leveling logic contained here
	//Start at level 0,  with ::initial_clicks of clicks
	//Getting level 1 requires ::first_level_clicks
	//Each level after 1 requires the number of clicks in the previous level, times ::modifier (rounded up)
	static function calcTotal($endLevel,$echo=False,$hcore=False) {
		$json = array();
		$prev = self::first_level_clicks + ($hcore ? self::hcoreadd : 0);
		$total = self::first_level_clicks + ($hcore ? self::hcoreadd : 0);
		for($level=0;$level < $endLevel; $level++) {
			$json[] = array("level"=>$level+1,"total"=>$total,"increase"=>$prev);
			$prev = ceil($prev * (self::modifier + ($hcore ? self::hcoremod : 0)));
			$total +=  $prev;
		}
		if($echo) return $json;
		return $total;
	}
	static function calcRemaining($level,$clicks,$hcore=False) {
		$left = self::calcTotal($level,False,$hcore) - $clicks;
		if($left==0) $left = self::calcTotal($level+1,False,$hcore) - $clicks;
		return $left;
	}
	// Database Functions
	static function connect() {
		global $CONFIG;
		
		$db = new mysqli($CONFIG['sql_host'],$CONFIG['sql_user'],$CONFIG['sql_pass'],$CONFIG['sql_db']);
		if($db->connect_error) {
			logError('Connect Error ('.$db->connect_errno.') '.$db->connect_error);
		}
		return $db;
	}
	static function arrayQuery($sql,$column=null,$db=null) {
		if(is_null($db)) {$close = true; $db = self::connect(); } else { $close = false; }
		$result = $db->query($sql);
		if($result === False) {
			logDebug("Query failed - ".$result->error);
			return $result->error;
		}
		if($result->num_rows == 0) {
			$result->close();
			return False;
		}
		$array = $result->fetch_assoc();
		$result->close();
		if($close) $db->close();
		if($column != null) {
			return $array[$column];
		} else {
			return $array;
		}
	}
	static function fullQuery($sql,$db=null) {
		if(is_null($db)) {$close = true; $db = self::connect(); } else { $close = false; }
		$result = $db->query($sql);
		if($result === False) {
			logDebug("Query failed - ".$result->error);
			return $result->error;
		}
		if($result->num_rows == 0) {
			$result->close();
			return False;
		}
		$all = array();
		while($row =  $result->fetch_assoc()) {
			$all[] = $row;
		}
		$result->close();
		if($close) $db->close();
		return $all;
	}
	
	//Magic Methods
	function toString() {
		return "User #".
			$this->id.
			" '".
			$this->name.
			"' ip=".
			$this->ip.
			" Admin? ".
			($this->admin ? 'True' : 'False').
			" Banned? ".			
			($this->banned ? 'True' : 'False').
			"; ";
	}
	function __wakeup() {
		$this->db = self::connect();
	}
}