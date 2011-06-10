<?php

class User
{
	const VERSION = 10;

	const initial_clicks = 0;
	const first_level_clicks = 10;

	private $id;
	private $name;
	private $clicks;
	private $level;
	private $ip;
	private $password;
	private $admin;

	private $color;

	private $db;
	private $error;
	private $fatal=False;

	function __construct($username, $password, $ip) {
		$this->connect();

		$this->error = new Error();

		//Already logged in? No? Better verify you exist.
		$this->id = $this->arrayQuery('SELECT id FROM users WHERE username="'.sqlite_escape_string($username).'" LIMIT 1','id');
		//Not a real username, lets make you!
		if($this->id===False) {
			$this->error->add('USER_NOT_AVAILABLE');
			$this->db->query('INSERT INTO users(username,password,ip) VALUES("'.sqlite_escape_string($username).'","'.sqlite_escape_string($password).'","'.sqlite_escape_string($ip).'")');
			$this->id = $this->db->lastInsertRowid();
			$this->initializeUser();
		}

		//Now, what is your information?
		$temp = $this->arrayQuery('SELECT * FROM users WHERE id="'.($this->id).'" LIMIT 1');
		$this->name	= $temp['username'];
		$this->password	= $temp['password'];
		$this->clicks	= $temp['clicks'];
		$this->level	= $temp['level']; //Not actually used anywhere...
		$this->color	= new Color($temp['color']);
		$this->ip	= $temp['ip'];
		$this->admin	= $temp['admin'];

		//Are you really you?
		if( !$by_id && $password != $this->password ) {
			$this->error->add('INVALID_PASSWORD');
			$this->fatal = True;
			return;
		}
		if( $ip != $this->ip ) {
			$this->error->add('IP_MISMATCH');
			$this->db->query('UPDATE users SET ip="'.sqlite_escape_string($ip).'" WHERE id="'.($this->id).'"');
		}

		//Lets allow us to use this later
		$_SESSION['id'] = $this->id;
		$this->setLevel($this->recalcLevel());
	}

	//Getters
	function getName() { return $this->name; }
	function getClicks() { return $this->clicks; }
	function getLevel() { return $this->level; }
	function getIP() { return $this->ip; }
	function getAdmin() { return $this->admin; }
	function getSuccess() { return !$this->fatal; }
	function getError() { return $this->error; }
	function getColor() { return $this->color; }
	function getArray() { return array(
			"Username" => $this->name,
			"Clicks" => $this->Clicks,
			"Level" => $this->Level,
			"IP" => $this->ip,
			"Admin" => $this->admin,
			"Remaining" => $this->calcRemaining(),
			"Success" => !$this->fatal,
			"Error" => $this->error,
			"Color" => $this->color
		); }

	//Updater Logic
	function isUpdate() {
		return User::VERSION > self::VERSION;
	}
	function getUpdate() {
		$this->resync();
		return new User($this->name,$this->password,$this->ip);
	}

	//Main leveling logic contained here
	//Start at level 0, with initial_clicks of clicks
	//Getting level 1 requires first_level_clicks
	//Each level after 1 requires the number of clicks in the previous level, times 1.07 (rounded up)
	function calcTotal($endLevel) {
		$prev = self::first_level_clicks;
		$total = self::first_level_clicks;
		for($level=0;$level < $endLevel; $level++) {
			$prev = ceil($prev * 1.07);
			$total +=  $prev;
		}
		return $total;		
	}
	function calcRemaining() {
		$left = $this->calcTotal($this->level) - $this->clicks;
		if($left==0) $left = $this->calcTotal($this->level+1) - $this->clicks;
		return $left;		
	}

	function initializeUser() {
		$this->setClicks(self::initial_clicks);
		$this->resync();
	}

	function incrementClicks() {
		$this->setClicks($this->clicks + 1);
	}

	function setColor($code) {
		$temp = new Color($code);
		if(
			($this->color->isDefault() && $temp->getModifier()=='normal') || 
			($this->level >= 50 && $this->color->getName() == $temp->getName()) ||
			$this->level >= 75
		) $this->color->setColor($code);
		$this->resync();
	}

	//Chat Functions
	function addChat($mes) {
		$this->db->query('INSERT INTO chat (name,message,color,level,ip) VALUES ("'.$this->name.'","'.sqlite_escape_string($mes).'","'.$this->color->getHex().'","'.$this->level.'","'.$this->ip.'")');
	}

	//Private Functions
	private function setClicks($clicks) {
		$this->clicks = $clicks;
		$this->setLevel($this->recalcLevel());
		if(($this->clicks % 50)==0) $this->resync();
	}
	private function setLevel($level) {
		$old = $this->level;
		$this->level = $level;
		if($old != $level) $this->resync();
	}
	private function recalcLevel() {
		for($level=0; $this->clicks >= $this->calcTotal($level); $level++);
		return $level;
	}

	private function arrayQuery($sql,$column=null) {
		$result = $this->db->query($sql);
		if($result->numRows()==0) return False;
		$array = $result->fetch();
		if($column != null) {
			return $array[$column];
		} else {
			return $array;
		}
	}

	private function resync() {
		$result = $this->db->query('UPDATE users SET clicks="'.sqlite_escape_string($this->clicks).'", level="'.sqlite_escape_string($this->level).'", color="'.sqlite_escape_string($this->color).'" WHERE id="'.($this->id).'"');
		if($result === False) {
			$this->error->add('FAILED_RESYNC',sqlite_error_string($this->db->lastError()));
		} elseif($this->db->changes() == 0) {
			$this->error->add('EMPTY_RESYNC');
		} elseif($this->db->changes() >1) {
			$this->error->add('EXTRA_RESYNC');
		}
	}

	private function connect() {
		//Initialize Database connection
		$this->db = sqlite_factory('clickquest.sql');
	}

	//Magic Methods
	function __toString() {
		$return = '';
		$values = array(
			'id',
			'name',
			'clicks',
			'level',
			'ip',
			'password',
			'admin',
			'error',
		//	'error->toString()',
			'fatal'
		);
	//	$return .= '<pre>';
		foreach($values as $value) {
			$return .= '$this->'.$value.' = '.$this->$value."\n";
		}
	//	$return .= '</pre>';
		return $return;
	}

	function __sleep() {
		$this->error->log();
		return ($this->fatal ? array() : array('id','name','password','clicks','level','ip','admin','color'));
	}

	function __wakeup() {
		$this->connect();
		$this->error = new Error();
	}
}

?>