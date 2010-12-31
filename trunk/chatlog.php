<?php
require_once("../config.php");
date_default_timezone_set('America/New_York');
function getChatPage($pid,$results){
	global $CONFIG;
	$db = new mysqli($CONFIG['sql_host'],$CONFIG['sql_user'],$CONFIG['sql_pass'],$CONFIG['sql_db']);
	$return='';
	$r= $db->query('SELECT COUNT(*) FROM chat WHERE id >= 0');
	$lines=$r->fetch_row();
	$r->close();
	$lines=(int) $lines[0];
	if($results<1 || !is_int($results)){
		$results=20;
	}
	$pages=round(($lines-(($results/2)+1))/$results);
	if($pid<0 || !is_int($pid)){
		$pid=0;
	} elseif($pid>$pages){
		$pid=$pages;
	}
	$start=$pid*$results;
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
if (isset($_GET['getlog'])){
	echo json_encode(getChatPage((int)$_POST['pgid'],(int)$_POST['res']));
} else{?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CLICKQUEST Chatlog Browser</title>
		<style type="text/css">
@font-face {
	font-family: "DOS";
	src: url("style/dos.eot");
	src: local("☺"), url("style/dos.woff") format("woff"), url("style/dos.ttf") format("truetype"), url("style/dos.svg#webfontGl3kt1Pv") format("svg");
	font-weight: normal;
	font-style: normal;
}
body {
	background: none repeat scroll 0 0 black;
}
a:link, a:visited, a:active{
	color:white;
	border-width:2px;
	border-color:white;
}
.info {
	color:white;
	display:inline;
	padding-left:1em;
}
span {
	margin: 0px;
	padding: 0px;
	display: block;
	font-size: 1em;
	cursor: auto;
	font-family: "DOS","Courier",sans-serif;
}
#logctrl{
	border: 1px solid gray;
	color: white;
	z-index:9999;
}
#logctrl a{
	padding-left:1em;
	padding-right:2em;
}
		</style>
		<script type="text/javascript" src="script/jquery.js"></script>
		<script type="text/javascript" src="script/cycle.js"></script>
		<script type="text/javascript" src="script/extend.js"></script> 
		<script type="text/javascript">
		var pid=0;
		function loadLog(a) {
			if (a==0){
				pid=$(".pid").val()-1;
			} else{
				pid=pid+a;
			}
			var results=$(".res").val();
			$('.pid').keyup(function(event) {if(event.keyCode=='13') {loadLog(0);}});
			$('.res').keyup(function(event) {if(event.keyCode=='13') {loadLog(0);}});
			$.post("chatlog.php?getlog",{ pgid : pid , res : results  },function(data) {
				var arg = $.parseJSON(data);
				pid=arg.pid;
				$(".info").html("Showing page: "+(arg.pid+1)+" of "+(arg.pages+1)+" | line nr: "+(arg.pid*arg.results)+" to "+((arg.pid+1)*arg.results)+" of "+(arg.lines)+" lines");
				$(".pid").val(arg.pid+1);
				$("#log").html(arg.log);
				$('.res').val(arg.results);
				return;
			});
		}
		loadLog(0);
		</script>
	</head>
	<body>
	<div id="logctrl">
		<span class="info">no log</span><br/>
		<a href="javascript:loadLog(-1)">Previous</a>
		<input type="text" class="pid" value="1" size="5"/>
		<a href="javascript:loadLog(0)">GO!</a>
		<a href="javascript:loadLog(1)">Next</a>
		Results per page: <input type="text" class="res" value="20" size="3"/>
	</div>
	<div id="log">
	</div>
	</body>
</html><?php } ?>