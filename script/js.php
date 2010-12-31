<?php

require("../include/color.php");
require("../include/packer.php");
ob_start("packer");

function packer($str) {
	$packer = new JavaScriptPacker($str,'High ASCII');
	return $packer->pack();
}

header("Content-type: application/x-javascript");
?>
$(document).ready(function() {	
	$('#logout').click(function(event) { 
		$.getJSON("api.php?logOut",function(data) {
			if(data.success) {
				logout();
				initializeScreen();
			} else {
				$('<div></div>')
					.dialog({
						autoOpen: false,
						title: 'ERROR',
						buttons: {
							'Yes': function(){
							 $(this).dialog('close'); 
							 logout(); 
						},
						'No': function(){
							$(this).dialog('close');
						}
					}
				}).html('Logout Failed! Try again?').dialog('open');
			}
			return;
		});
		event.preventDefault();
		return;
	});
	initializeScreen();	
	return;
});
function cycleify() {
	$('#others').cycle("destroy");
	$('#others').cycle({ 
	    fx:     'scrollHorz', 
	    prev:   '#prev', 
	    next:   '#next', 
	    containerResize: false,
	    timeout: 0 
	});
	return;
}
function tabify() {
	$('#right').tabs("destroy");
	$('#right').tabs({
		fx: {
			height: 'toggle',
			opacity: 'toggle'
		}
	});
	return;
}
function grayify() {
	$('#username').css('color','grey');
	$('#username').val('Username');
	$('#username').focus(function(){
		$(this).val('');
		$(this).css('color','black');
		$(this).unbind();
	});

	$('#password').css('color','grey');
	$('#password').val('Password');
	$('#password').focus(function(){
		$(this).val('');
		$(this).css('color','black');
		$(this).unbind();
	});
	return;
}
function actionify() {
	$('.colorselector').click(function(event) {
		var color= rgb2hex($(this).css('color'));
		$.post('api.php?setColor',{ hex: color },function(data) {
			var arg = $.parseJSON(data);
			if(arg.success) {
				updateColor(color);
				return;
			} else {
				$('<div></div>')
					.dialog({
						autoOpen: false,
						title: 'ERROR',
						buttons: {
						'Alright': function(){
							$(this).dialog('close');
						}
					}
				}).html('Failed to select color, probably because it has already been set.').dialog('open');
				return;
			}
		});
		return;
	});
	$('body').click(function(event) { click(); return; });
}
function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    return "" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}
function initializeScreen() {
	$.getJSON("api.php?isLoggedIn",function(data) {
		if(data.loggedin) {
			loadUser();
		} else {
			$('#left').html('<form id="login" method="post" action="index.php">'+
				'<input type="text" id="username" name="username" value="Username" /><br />'+
				'<input type="password" id="password" name="password" value="Password"/><br />'+
				'<input type="submit" id="submit" name="submit" value="Login" />'+
				'<input type="reset" id="reset" name="reset" value="Clear" />'+
				'</form>');
			$('#right').html('<?php 
			$echo = <<<EOF
				<ul>
					<li><a href="#welcome">Welcome</a></li>
				</ul>
				<div id="welcome" class="content">
					Welcome to the game, you fool.<br />
					<br />
					More information will appear here shortly.
				</div>
EOF;
			echo str_replace(array("'","\r","\n","\t"),array("\'",''),$echo);
			?>');
		}
		grayify();
		tabify();
		$('#login').submit(function(event) {
			$.post('api.php?isUser',{ username: $('#username').val() },function(data) {
				var arg = $.parseJSON(data);
				if(arg.exists) {
					login();
					return;
				} else {
					$('<div></div>')
						.dialog({
							autoOpen: false,
							title: 'WARNING',
							buttons: {
								'Yes': function(){
								 $(this).dialog('close'); 
								 login(); 
							},
							'No': function(){
								$(this).dialog('close');
							}
						}
					}).html('This user does not exist! Create it?').dialog('open');
					return;
				}
			});
			event.preventDefault();
			return false;
		});
		return;
	});
	return;
}
function login() {
	var pass = $.md5($.sha1($.md5($('#password').val())));
	$.post('api.php?logIn', { username: $('#username').val(), password: pass }, function(data) {
		var arg = $.parseJSON(data);
		if(arg.success) {
			loadUser();
		} else {
			$('<div></div>')
			.dialog({
				autoOpen: false,
				title: 'ERROR',
				buttons: {
					'Okay': function(){
						$(this).dialog('close');
					}
				}
			}).html('Login Failed because '+arg.error+', Please fix and try again.').dialog('open');
		}
		return;
	});
	return;
}
function logout() {
	$('body').stopTime("heartbeat");
	$('body').click(function(event) { return; });
	$('body').stopTime("session");
	$('body').stopTime("general");
	arg = "";
	click.fail = 0;
	click.idle = false;
	click.last = 0;
	heartbeat.start = -1;
	heartbeat.level = -1;
	heartbeat.newClicks = 0;
	heartbeat.storedClicks = -1;
	heartbeat.ip = "-2.-2.-2.-2";
	heartbeat.color = "FFFFFF";
	heartbeat.user = "";
	heartbeat.murmur = true;
	return;
}
function calcTotal(num,display) {
	$.post('api.php?calcTotal', { level: num }, function(data) {
		var arg = $.parseJSON(data);
		heartbeat.left = 1*arg.clicks;
		if(display) displayUser();
		return;
	});
	return;
}
function displayUser() {
	updateColor(heartbeat.color);
	$('#left').html('<div id="name">'+heartbeat.user+'</div>'+
				'<div id="level">'+heartbeat.level+'</div>'+
				'<div id="total">total: '+(1*heartbeat.newClicks + 1*heartbeat.storedClicks)+' clicks</div>'+
				'<div id="remain">next: '+(heartbeat.left - (1*heartbeat.newClicks + 1*heartbeat.storedClicks))+' clicks</div>'+
				'<div id="bonus">'+''+'</div>'+
				'<div id="server">'+
					'<div id="ip">SERVER IP: '+heartbeat.ip+'</div>'+
					'<div id="session">SESSION TIME: '+timeFormat(login.session)+'</div>'+
					'<div id="logintime">ON SINCE: '+dateFormat(new Date(heartbeat.start*1000))+'</div>'+
					'<div id="totaltime">TOTAL TIME: '+timeFormat(login.session + login.total)+'</div>'+
				'</div>');
	$('#right').html('<?php 
			$echo = <<<EOF
				<ul>
					<li><a href="#chat">Chat</a></li>
					<li><a href="#stats">Statistics</a></li>
					<li><a href="#team">Meet The Team</a></li>
					<li><a href="#log">Changelog</a></li>
					<li><a href="#color">Color List</a></li>
					<li><a href="#chatlog">Chatlog</a></li>
				</ul>
				<div id="chat">
					<div id="message" class="content"></div>
					<div class="sender">
						<input type="text" class="mes" />
						<input type="button" class="messend" value="Send" /><br />
						<span class="reporter">CHAT BROKEN :(</span>
					</div>
				</div>
				<div id="stats" class="content"></div>
				<div id="team" class="content">
					<span style="font-weight: bold; font-size: 1.24em">Alex Steacy - Innovator, Man of Action, Emmisary of Awesome</span><br />
					<span>Alex leads the team with a vision, a vision so horrendous and vile that it must be purified through the souls of 100 newborn kittens before it is passed down. Without him, ClickQuest would never had been born.</span><br />
					<br />
					<span style="font-weight: bold; font-size: 1.24em">Fugiman - Head Developer</span><br />
					<span>Having written the initial two versions of ClickQuest, Fugi now relaxes at an unknown tropical island, sipping Coconut Rum with Coke while enjoying the comforts of many young nubile women.</span><br />
					<br />
					<span style="font-weight: bold; font-size: 1.24em">Nmaster64 - Server Administrator</span><br />
					<span>Diety of Hardware, Nmaster controls the carcass of ClickQuest. His valiant efforts prevent your idiotic actions from preventing the whirring of the time-leeching machinery.</span><br />
					<br />
					<span style="font-weight: bold; font-size: 1.24em">Hagios - Security Analyst</span><br />
					<span>Bugs? Injections? Cross Site Scripting? Pathetic. All shrivel before the gaze of Hagios. Think you know a way to get a quick click? Hagios was already there, and now you'll be banned for it. See you later.</span><br />
					<br />
					<span style="font-weight: bold; font-size: 1.24em">Invariel - Quality Control, Backup Developer</span><br />
					<span>Master of All Trades, Jack of None, Invariel gets the odd jobs done. If Fugi or Hagios feels lazy, he steps in. And if YOU are a dick, he takes you out.</span><br />
					<br />
					<span style="font-weight: bold; font-size: 1.24em">Ganonmaster - iPhone Dev</span><br />
					<span>Bringing the pain to your phone, so you can click on the go. </span><br />
					<br />
					<span style="font-weight: bold; font-size: 1.24em">Fleppensteyn - Random Dev</span><br />
					<span>I can haz text about me?</span><br />
				</div>
				<div id="log" class="content">
					<h2>Changelog</h2>
					<h3>1.1</h3>
					<b>1.4</b>
					<ul>
						<li>Added the chatlog in separate tab and on a separate page</li>
						<li>Hyperlinks become clickable when posted in chat</li>
						<li>Modified stats page to include fails</li>
					</ul>
					<b>1.3</b>
					<ul>
						<li>Back-End changes to support admins screwing with clickcounts without loss of true click count.</li>
						<li>Made color changes from 75 onward in a seperate tab</li>
					</ul>
					<b>1.2</b>
					<ul>
						<li>Modified stats page. (Removed latest click (look at online users). Added color for the leaderboard. Made the Color break-down chart easier to read.)</li>
					</ul>
					<b>1.1</b>
					<ul>
						<li>Stats page now shows your ranking and LRR crew rankings</li>
					</ul>
					<b>1.0</b>
					<ul>
						<li>Added a statistics page</li>
						<li>Chat saves scroll state</li>
						<li>Log out after 10 minutes of idling</li>
						<li>Slows updates after 2 minutes of idling (and stops the session timer)</li>
						<li>Modified anti-cheat procedure to (hopefully) be less buggy and more secure</li>
						<li>Random backend aesthetical changes</li>
					</ul>
					<b>0.3</b>
					<ul>
						<li>Added support for the LRR tag</li>
						<li>Updated specific users with the new LRR tag</li>
					</ul>
					<b>0.2</b>
					<ul>
						<li>Added a list of Team members</li>
						<li>Added a Changelog</li>
					</ul>
					<b>0.1</b>
					<ul>
						<li>Added Unicode support to chat</li>
						<li>Updated Alex's Twitter link</li>
						<li>Made website links open in a new window</li>
					</ul>
				</div>
				<div id="chatlog">
					<div id="logctrl">
						<span class="info">no log</span><br/>
						<a href="javascript:loadLog(-1)">Previous</a>
						<span><input type="text" class="pid" value="1" size="5"/>
						<a href="javascript:loadLog(0)">GO!</a></span>
						<a href="javascript:loadLog(1)">Next</a>
						<a href="chatlog.php" target="_blank">Seperate Chatlog</a>
					</div>
					<div id="logdata" class="content"></div>
				</div>
EOF;
			echo str_replace(array("'","\r","\n","\t"),array("\'",''),$echo); 
			$str = "<div id='color'>";
			foreach(Color::colorArray() as $name=>$arr) {
				$str .= "<span class='colorholder'>";
				foreach($arr as $mod=>$hex) {
					$str .= "<a class='colorselector' style='color:#".$hex.";'>".($mod=='normal' ? '' : $mod.' ').$name."</a><br />";
				}
				$str .= "</span>";
			}
			$str .= "</div>";
			echo str_replace(array("'","\r","\n","\t"),array("\'",''),$str); 
			?>');
			tabify();
			updateGeneral();
			
			click.fail = 0;
			sendmes.scroll = true;
			loadLog(0);
			$('.pid').keyup(function(event) {if(event.keyCode=='13') {loadLog(0);}});
			$('.messend').mouseup(sendmes);
			$('.mes').keyup(function(event) {if(event.keyCode=='13') {sendmes();}});
}
function sendmes() {
	if($('.mes').val()=='') return;
	click.last = Math.round(((new Date()).getTime()-Date.UTC(1970,0,1))/1000);
	var mess = $('.mes').val();
	$('.mes').val('');
	$('.reporter').html('Sending message...');
	$.post('api.php?addChat',{ message: mess },function(data) {
		var arg = $.parseJSON(data);
		if(arg.success) {
			$('.reporter').html('');
			sendmes.scroll = true;
		} else {
			$('.reporter').html('MESSAGE FAILED');
				$('<div></div>')
				.dialog({
					autoOpen: false,
					title: 'ERROR',
					buttons: {
						'Fine!': function(){
							 $(this).dialog('close'); 
						}
					}
				}).html('Message Failed to Send. Try again?').dialog("open");
		}
	});
}
var pid=0;
function loadLog(a) {
	if (a!=0){
		pid=this.pid+a;
	} else{
		pid=$(".pid").val()-1;
	}
	$.post("chatlog.php?getlog",{ pgid : pid },function(data) {
		var arg = $.parseJSON(data);
		pid=arg.pid;
		$(".info").html("Showing page: "+(arg.pid+1)+" of "+(arg.pages+1)+" | line nr: "+(arg.pid*20)+" to "+((arg.pid+1)*20)+" of "+(arg.lines)+" lines");
		$(".pid").val(arg.pid+1);
		$("#logdata").html(arg.log);
		return;
	});
}
function loadUser() {
	$.getJSON("api.php?loadUser",function(data) {
		heartbeat.start = data.start;
		heartbeat.level = data.level;
		heartbeat.newClicks = 0;
		heartbeat.storedClicks = data.clicks;
		heartbeat.ip = data.ip;
		heartbeat.color = data.color;
		heartbeat.user = data.user;
		heartbeat.murmur = true;
		
		login.session = 0;
		login.total = 1*data.totalTime;
		$('body').everyTime("1s","session",function() { login.session += 1; refreshTimers(); return; });
		
		calcTotal(heartbeat.level,true);
		$('body').click(function(event) { click(); return; });
		
		var time = Math.round(((new Date()).getTime()-Date.UTC(1970,0,1))/1000);
		heartbeat(time);
		
		$('body').everyTime("3s","heartbeat",function() {
			var time = Math.round(((new Date()).getTime()-Date.UTC(1970,0,1))/1000);
			heartbeat(time);
			return;
		});
		
		$('body').everyTime("60s","general",function() {
			updateGeneral();
			return;
		});
		return;
	});
	return;
}
function updateGeneral() {
	$.getJSON("api.php?getGeneral",function(data) {
		$('#stats').html(data.stats);
		return;
	});
	return;
}
function click() {
	//Hagios nerf, prevents over clicking.
	if ( typeof click.fail == 'undefined' ) {
		click.fail = 0;
	}
	if(click.fail==1) return;
	click.fail = 1;
	click.last = Math.round(((new Date()).getTime()-Date.UTC(1970,0,1))/1000);
	click.idle = false;
	$('.reporter').oneTime("60ms",function() { click.fail = 0; });
	
	heartbeat.newClicks = heartbeat.newClicks +1;
	$('#total').html('total: '+(1*heartbeat.newClicks + 1*heartbeat.storedClicks)+' clicks');
	if((heartbeat.left - (1*heartbeat.newClicks + 1*heartbeat.storedClicks)) == 0) {
		heartbeat.level = 1*heartbeat.level + 1;
		$('#level').html(heartbeat.level);
		$('#bonus').html('LEVEL UP!');
		calcTotal(heartbeat.level,false);
	}
	if(1*heartbeat.level > 99) {
		$('#overlay').css('display','block');
	}
	$('#remain').html('next: '+(heartbeat.left - (1*heartbeat.newClicks + 1*heartbeat.storedClicks)+' clicks'));
	return;
}
function updateColor(hex) {
	$('#left').css('color','#'+hex);
	heartbeat.color = hex.toUpperCase();
}
function heartbeat(time) {

	if(!click.idle && click.last+120 < Math.round(((new Date()).getTime()-Date.UTC(1970,0,1))/1000)) {
		click.idle = true;
		$('body').stopTime("heartbeat");
		$('body').everyTime("10s","heartbeat",function() {
			var time = Math.round(((new Date()).getTime()-Date.UTC(1970,0,1))/1000);
			heartbeat(time);
			return;
		});
		$('body').stopTime("session");
		$('<div></div>')
			.dialog({
				autoOpen: false,
				title: 'WARNING',
				buttons: {
					'I\'m Here!': function(){
						if(click.idle) {
							click.idle = false;
							click.last = Math.round(((new Date()).getTime()-Date.UTC(1970,0,1))/1000);
							$('body').stopTime("heartbeat");
							$('body').everyTime("3s","heartbeat",function() {
								var time = Math.round(((new Date()).getTime()-Date.UTC(1970,0,1))/1000);
								heartbeat(time);
								return;
							});
							$('body').everyTime("1s","session",function() { login.session += 1; refreshTimers(); return; });
						}
						$(this).dialog('close'); 
					}
				}
			}).html('You have been idle for 2 minutes, therefore auto-updates have slowed down and session time has stopped. Please click to continue.').dialog("open");
	}
	if(heartbeat.murmur) {
		$('.reporter').html('Fetching latest messages...');
		$.getJSON("api.php?getChat",function(data) {
			$('.reporter').html('');
			if(!sendmes.scroll) sendmes.scroll = isScrollBottom();
			$('#message').html(data.chat);
			if(sendmes.scroll) $('#message').scrollTop(999999);
			sendmes.scroll = false;
			return;
		});
		heartbeat.murmur = false;
	} else {
		heartbeat.sentClicks = heartbeat.newClicks;
		var hash = genhash(time);
		$('.reporter').html('Syncing with Server...');
		$.post('api.php?heartbeat',{ action: hash, time: login.session+login.total},function(data) {
			$('.reporter').html('Parsing...');
			var arg = $.parseJSON(data);
			$('.reporter').html('');
			if(arg.success) {
				if(!sendmes.scroll) sendmes.scroll = isScrollBottom();
				$('#message').html(arg.chat);
				$('#others').html(arg.online);
				$('#onlinenumber').html(arg.number);
				$('#bonus').html(arg.action);
				cycleify();
				actionify();
				if(sendmes.scroll) $('#message').scrollTop(999999);
				sendmes.scroll = false;
				heartbeat.newClicks = heartbeat.newClicks - (arg.clicks*1 - heartbeat.storedClicks);
				heartbeat.storedClicks = arg.clicks;
			} else {
				logout();
				$('.reporter').html('D/C');
				$('<div></div>')
				.dialog({
					autoOpen: false,
					title: 'WARNING',
					buttons: {
						'Logout': function(){
							 initializeScreen(); 
							 $(this).dialog('close'); 
						}
					}
				}).html('You have disconnected from the server. Please login again.').dialog("open");
			}
			return;
		});
		heartbeat.murmur = true;
	}
	return;
}
function genhash(time) {
	var hash1 = $.md5('' + time + heartbeat.level + heartbeat.sentClicks);
	var hash2 = $.sha1('' + heartbeat.storedClicks + heartbeat.ip + heartbeat.color);
	var hash3 = $.sha1('' + heartbeat.start + hash1 + heartbeat.storedClicks);
	var hash4 = $.md5('' + heartbeat.newClicks + hash2 + heartbeat.user);

	var recover1 = $.base64Encode("" + strPad(""+time,40) + strPad(""+hash3,40) + strPad(""+hash4,40) + strPad(""+heartbeat.user,40));
	var recover2 = $.base64Encode("" + strPad(""+heartbeat.sentClicks,40) + strPad(""+hash1,40) + strPad(""+hash2,40) + strPad(""+heartbeat.storedClicks,40));
	var recover3 = $.base64Encode("" + strPad(""+heartbeat.start,40) + strPad(""+hash1,40) + strPad(""+hash3,40) + strPad(""+heartbeat.level,40));
	var recover4 = $.base64Encode("" + strPad(""+heartbeat.color,40) + strPad(""+hash2,40) + strPad(""+hash4,40) + strPad(""+heartbeat.ip,40));
	
	var arrg = new Array(recover1.length, recover2.length, recover3.length, recover4.length);
	var length = Math.max.apply( Math , arrg );
	
	var part1 = ""+strPad(""+recover1, 1*length);
	var part2 = ""+strPad(""+recover2, 1*length);
	var part3 = ""+strPad(""+recover3, 1*length);
	var part4 = ""+strPad(""+recover4, 1*length);
	
	var zebra = "";
	for(var i = 0; i < length; i++) {
		zebra = zebra + part1[i];
		zebra = zebra + part2[i];
		zebra = zebra + part3[i];
		zebra = zebra + part4[i];
	}
	
	return $.base64Encode(zebra);
}
function dateFormat(d) {
	var a_p = "";
	var curr_hour = d.getHours();
	if (curr_hour < 12) { a_p = "AM"; }
		else { a_p = "PM"; }
	if (curr_hour == 0) { curr_hour = 12; }
	if (curr_hour > 12) { curr_hour = curr_hour - 12; }

	var curr_min = d.getMinutes();
	curr_min = curr_min + "";
	if (curr_min.length == 1) { curr_min = "0" + curr_min; }

	return curr_hour + ":" + curr_min + a_p + " " + (d.getMonth()+1) + "/" + d.getDate() + "/" + d.getFullYear();
}
function timeFormat(t) {	
	var s = t%60;
	var rm = (t-s)/60;
	var m = rm%60;
	var h = (rm-m)/60;
	
	s = s + "";
	m = m + "";
	h = h + "";
	if (s.length == 1) { s = "0" + s; }
	if (m.length == 1) { m = "0" + m; }
	
	return h + ":" + m + ":" + s;
}
function isScrollBottom() {
	return $("#message").attr("scrollHeight") - $('#message').height() <= $('#message').scrollTop() + 160;
}
function refreshTimers() {
					$('#session').html('SESSION TIME: '+timeFormat(login.session));
					$('#totaltime').html('TOTAL TIME: '+timeFormat(login.session + login.total));
}