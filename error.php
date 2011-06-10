<?php

class Error
{
	private $error	=	0;
	private $db;

	private $CONSTANTS = array(
		'USER_NOT_AVAILABLE'	=>	1,
		'INVALID_PASSWORD'	=>	2,
		'IP_MISMATCH'		=>	4,
		'INVALID_ERROR_TYPE'	=>	8,
		'FAILED_RESYNC'		=>	16,
		'EMPTY_RESYNC'		=>	32,
		'EXTRA_RESYNC'		=>	64
	);

	private $MESSAGES = array(
		1	=>	"User Not Found, New User Created",
		2	=>	"Invalid Password, Please Retry",
		4	=>	"IPs did not match, updated for new location.",
		8	=>	"An invalid type of variable was passed to be added to the error code",
		16	=>	"The attempt to resyncronise the USER class with the SQLite database failed, see trace.",
		32	=>	"The resync with the SQLite database succeeded, but affected no rows.",
		64	=>	"The resync with the SQLite database succeeded, but affected too many rows."
	);

	private $TRACE = array();

	function __construct($code=0) {
		//Initialize Database connection
		$this->db = sqlite_factory('clickquest.sql');
		$this->error = $this->intVal($code);
	}

	function add($code,$trace=null) {
		$this->error = $this->error | $this->intVal($code);
		if(!is_null($trace)) {
			foreach($trace as $key=>$value) {
				$this->TRACE[$this->intval($key)] = $value;
			}
		}
	}

	function remove($code) {
		$this->error = $this->error & ~$this->intVal($code);
		foreach($this->factoredArray($this->intVal($code)) as $key) {
			unset($this->TRACE[$key]);
		}
	}

	function includes($code) { 
		//WARNING: Returns true if ANY of $code is included
		//AKA: If 3 is passed, it will return true if 1 OR 2 is included, not both
		return $this->error & $this->intVal($code);
	}

	private function intVal($code) {
		if(is_int($code)) {
			return $code;
		} elseif(is_string($code)) {
			$return = 0;
			foreach(explode('|',$code) as $constant) {
				$return += $this->CONSTANTS[trim($constant)];
			}
			return $return;
		} else{
			//WHAT DO I DO?
			return 8;
		}
	}

	private function factoredArray($code=null) {
		$i=1;
		if(is_null($code)) $code=$this->error;
		$array = array();
		while($i<=$code) {
			if($i & $code) $array[] = $i;
			$i *= 2;
		}
		return $array;
	}

	function toConstArray($code=null) {
		$array = array();
		$lookup = array_flip($this->CONSTANTS);
		foreach($this->factoredArray($code) as $val) {
			$array[] = $lookup[$val];
		}
		return $array;
	}

	function toMessageArray($code=null) {
		$array = array();
		foreach($this->factoredArray($code) as $val) {
			$message = $this->MESSAGES[$val];
			if(isset($this->TRACE[$val])) $message .= ' trace='.$this->TRACE[$val];
			$array[] = $message;
		}
		return $array;
	}

	function log() {
		if($this->error > 0) error_log($this->toString()."\n",3,"error.log");
	}

	function toString() {
		return count($this->factoredArray()).' Errors Found (Code='.$this->error.'): "'.implode('","',$this->toMessageArray()).'"';
	}

	function __toString() { return $this->toString(); }
	function __wakeup() { $this->db = sqlite_factory('clickquest.sql'); }

	function toHTML() {
		return '<span class="error">'.count($this->factoredArray()).' Errors Found: <ul><li>'.implode('</li><li>',$this->toMessageArray()).'</li></ul></span>';
	}
}
?>