<?php
require_once("include/core.php");
if(isset($_REQUEST['page'])&&(int)$_REQUEST['page']>0){
	$page=(int)$_REQUEST['page'];
}else{
	$page=1;
}
if(isset($_REQUEST['results'])&&(int)$_REQUEST['results']>0){
	$results=(int)$_REQUEST['results'];
}else{
	$results=20;
}
$r=$USER::getChatPage($page,$results);
?>
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
		$(document).ready(function() {	
			loadLog.pid=<?php echo $page; ?>;
			$('#next').click(function(event){
				event.preventDefault();
				loadLog.pid++;
				loadLog();
			});
			$('#form').submit(function(event) {
				event.preventDefault();
			});
			$('#go').click(function(event){
				if (loadLog.pid==$(".pid").val()){
					return;
				} else{
					loadLog.pid=$(".pid").val();
					loadLog();
				}
			});
			$('#prev').click(function(event){
				event.preventDefault();
				loadLog.pid--;
				loadLog();
			});
			$('.inp').keyup(function(event) {
				if(event.keyCode=='13') {
					loadLog.pid=$(".pid").val();
					loadLog();
				}
			});
		});
		function loadLog() {
			var results=$(".res").val();
			$.post("api.php?getChatLog",{ pgid : loadLog.pid , res : results  },function(data) {
				var arg = $.parseJSON(data);
				loadLog.pid=arg.pid;
				$(".info").html("Showing page: "+(arg.pid)+" of "+(arg.pages)+" | line nr: "+(((arg.pid-1)*arg.results)+1)+" to "+(arg.pid*arg.results)+" of "+(arg.lines)+" lines");
				$(".pid").val(arg.pid);
				$("#log").html(arg.log);
				$('.res').val(arg.results);
				return;
			});
		}

		</script>
	</head>
	<body>
	<div id="logctrl">
		<span class="info"><?php echo "Showing page: ".$r['pid']." of ".$r['pages']." | line nr: ".((($r['pid']-1)*$r['results'])+1)." to ".($r['pid']*$r['results'])." of ".($r['lines'])." lines"; ?></span><br/>
		<form action="chatlog.php" id="form">
		<a id="prev" href="<?php echo "chatlog.php?page=".($r['pid']-1)."&results=".($r['results']); ?>">Previous</a>
		<input type="text" class="pid" name="page" value="<?php echo $r['pid']; ?>" size="5"/>
		<input type="submit" id="go" value="GO!"/>
		<a id="next" href="<?php echo "chatlog.php?page=".($r['pid']+1)."&results=".($r['results']); ?>">Next</a>
		Results per page: <input type="text" class="res" name="results" value="<?php echo $r['results']; ?>" size="3"/>
		</form>
	</div>
	<div id="log"><?php echo $r['log'];?>
	</div>
	</body>
</html>