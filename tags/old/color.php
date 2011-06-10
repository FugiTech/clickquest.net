<?php

class Color
{
	private $name;
	private $modifier;
	private $hex;

	private $db;

	const DEFAULT_NAME = "White";
	const DEFAULT_HEX  = "FFFFFF";

	private $allowed = array(
		"red" =>    array("normal"=>"FF0000","dark"=>"990000","light"=>"FF6565"),
		"yellow" => array("normal"=>"FFFF00","dark"=>"999900","light"=>"FFFF65"),
		"green" =>  array("normal"=>"00FF00","dark"=>"009900","light"=>"65FF65"),
		"blue" =>   array("normal"=>"0000FF","dark"=>"000099","light"=>"6565FF"),
		"cyan" =>   array("normal"=>"00FFFF","dark"=>"009999","light"=>"65FFFF"),
		"magenta" =>array("normal"=>"FF00FF","dark"=>"990099","light"=>"FF65FF")
	);
	private $modifiers = array(
		'normal',
		'dark',
		'light'
	);

	function __construct($value=null) {
		$this->connect();
		$this->setColor($value);
	}

	function __toString() { return $this->toString(); }
	function __wakeup() { $this->connect(); }
	function toString() { return $this->hex; }
	function getName() { return $this->name; }
	function getModifier() { return $this->modifier; }
	function getHex() { return $this->hex; }
	function getArray() { return array('name'=>$this->name, 'modifier'=>$this->modifier, 'hex'=>$this->hex);}
	function format($mes,$class=null) { return '<span '.(is_null($class) ? '' : 'class="'.$class.'" ').'style="color: #'.$this->hex.';">'.$mes.'</span>'; }

	function setColor($value) {
		if(is_null($value)) {
				$this->name = self::DEFAULT_NAME;
				$this->modifier = "";
				$this->hex = self::DEFAULT_HEX;
		} elseif($this->isHex($value)) {
			$temp = $this->reverseLookup($value);
			if($temp === False) {
				$this->name = self::DEFAULT_NAME;
				$this->modifier = "";
				$this->hex = self::DEFAULT_HEX;
			} else {
				$this->name = $temp['name'];
				$this->modifier = $temp['modifier'];
				$this->hex = $value;
			}
		} elseif(strpos($value," ")===False && isset($this->allowed[$value])) {
			$this->name = $value;
			$this->modifier = "normal";
			$this->hex = $this->allowed[$value]["normal"];
		} else {
			$temp = explode(" ",$value);
			if(array_key_exists($temp[1],$this->allowed) && in_array($temp[0],array("normal","light","dark"))) {
				$this->name = $temp[1];
				$this->modifier = $temp[0];
				$this->hex = $this->allowed[$temp[1]][$temp[0]];
			} else {
				$this->name = self::DEFAULT_NAME;
				$this->modifier = "";
				$this->hex = self::DEFAULT_HEX;
			}
		}
	}

	function isDefault() {
		return $this->hex == self::DEFAULT_HEX;
	}
	function colorArray($filter=null) {
		if(empty($filter)) {
			return $this->allowed;
		} elseif(in_array($filter,$this->modifiers)) {
			$return = array();
			foreach($this->allowed as $name=>$array) {
				$return[$name] = $array[$filter];
			}
			return $return;
		} elseif(isset($this->allowed[$filter])) {
			return $this->allowed[$filter];
		} else {
			return False;
		}
	}

	private function isHex($code) {
		return preg_match('/^[0-9A-F]{6}$/i',$code);
	}

	private function reverseLookup($code) {
		foreach($this->allowed as $name=>$inner) {
			foreach($inner as $modifier=>$hex) {
				if($hex == $code) return array("name"=>$name,"modifier"=>$modifier);
			}
		}
		return False;
	}

	private function connect() {
		$this->db = sqlite_factory('clickquest.sql');
	}
}

?>