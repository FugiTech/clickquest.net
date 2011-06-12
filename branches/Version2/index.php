<?php require('include/core.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title><?php echo NAME.' V'.VERSION.'.'.MAJOR.'.'.MINOR.':'.REVISION ?></title>
		<script type="text/javascript" src="script/jquery.js"></script>
		<script type="text/javascript" src="script/ui.js"></script>
		<script type="text/javascript" src="script/cycle.js"></script>
		<script type="text/javascript" src="script/extend.js"></script> 
		<script type="text/javascript" src="script/js.php"></script>
		<link href="style/css.php" rel="stylesheet" type="text/css" />
<?php if(isset($_GET['small'])) { ?>		<link href="style/small-css.php" rel="stylesheet" type="text/css" />
<?php } ?>		<link href="style/ui.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-23812474-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
	</head>
	<body>
		<!--<div id="overlay">
			<img src="http://www.socalbubble.com/wp-content/uploads/2007/04/baby_crying_closeup.jpg" id="cry" />
			<h1 id="yell">JUST STOP CLICKING ALREADY!</h1>
			<div class="sender">
				<input type="text" class="mes" />
				<input type="button" class="messend" value="Send" /><br />
				<span class="reporter">CHAT BROKEN :(</span>
			</div></div>-->
		<div id="top">
			<div id="titleholder">
				<div id="title"><?php echo NAME.' VERSION '.VERSION.' PATCH '.MAJOR.' REVISION '.MINOR.'.'.REVISION ?></div>
				<div id="copy"><?php echo COPYRIGHT; ?></div>
			</div>
			<div id="control">
				<div id="logout">&gt; Logout</div>
				<div id="links">Twitter:
					<a href="http://twitter.com/Fugiman" target="_blank">Fugiman</a>
					<a href="http://twitter.com/Alex_LRR" target="_blank">Alex Steacy</a>
					<a href="http://twitter.com/loadingreadyrun" target="_blank">LRR</a>
					<br />Web:
					<a href="http://www.eximworks.org/" target="_blank">Eximworks</a>
					<a href="http://loadingreadyrun.com/" target="_blank">LRR</a>
					<a href="http://www.youtube.com/user/crapshotvideo" target="_blank">CrapShots</a></div>
			</div>
		</div>
		<div id="middle">
			<div id="left"></div>
			<div id="right">

			</div>
		</div>
		<div id="bottom">
			<div id="onlineheader">Online players (<span id="onlinenumber">??</span>)</div>
			<div id="prev">
					<div id="lt">&lt;</div>
					<div id="prevtext">PREV</div>
			</div>
			<div id="others"></div>
			<div id="next">
				<div id="gt">&gt;</div>
				<div id="nexttext">NEXT</div>
			</div>
		</div>
	</body>
</html>
<?php $_SESSION['hash'] = base64_encode(serialize($USER)); ?>