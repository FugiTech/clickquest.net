<?php
header("Content-type: text/css");
?>
h2, h3 { 
	margin-top: 0px;
	margin-bottom: 0px;
}

/*\
|*| The Over-Arching Container
\*/
#overlay {
	position: absolute;
	height: 100%;
	width: 100%;
	background: black;
	display: none;
	z-index: 9001;
	
  /* for IE */
  filter:alpha(opacity=60);
  /* CSS3 standard */
  opacity:0.6;
}
#cry {
	width: 800px;
	height: auto;
	margin: auto;
}
#yell {
	color: white;
	font-size: 6em;
	text-align: center;
	
  /* for IE */
  filter:alpha(opacity=100);
  /* CSS3 standard */
  opacity:1.0;
}

@font-face {
	font-family: 'DOS';
	src: url('dos.eot');
	src: local('☺'), url('dos.woff') format('woff'), url('dos.ttf') format('truetype'), url('dos.svg#webfontGl3kt1Pv') format('svg');
	font-weight: normal;
	font-style: normal;
}
::selection {
//	background: transparent;
}
::-moz-selection {
//	background: transparent;
}
html {
	height: 100%;
	width: 100%;
	margin: 0px;
	padding: 0px;
}
body {
	background: black;
	color: white;
	height: 100%;
	width: 100%;
	margin: 0px;
	padding: 0px;
//	overflow: hidden;

	font-family: "DOS", "Courier", sans-serif;
}
a { color: white; }

div { //border: 1px solid yellow;
	margin: 0px;
	padding: 0px;
}

span {
	margin: 0px;
	padding: 0px;
}
#top { padding-top: 15px; }
#titleholder {
	font-size: 1.8em;
	float: left;
}
#control {
	float: right;
	text-align: right;
}
#logout, #links {
	float: right;
	padding-right: 30px;
}
#logout { font-size: 2.4em; }
#links { font-size: 1.2em; }
#middle, #bottom { clear: both; }
#top, #middle {
	width: 95%;
	margin: auto;
}
#left, #right {
	width: 49.5%;
	height: 520px;
	float: left;
}
#logctrl{
	border: 1px solid gray;
	color: white;
	z-index:9999;
}
#logctrl *{
	margin-left:0.5em;
	margin-right:0.5em;
}

.content { overflow: auto; height: 450px; }
#message, #logdata { height: 400px; }
.chatline { display: block; }
#right { font-size: 1em; }
.mes, .messend , #logctrl input{
	background: black;
	border: 1px solid gray;
	color: white;
	z-index:9999;
}
.mes { width: 85%; }
#left {
	font-size: 1.4em;
	color: #00FFFF;
	text-align: center;
}
#name { font-size: 2em; }
#level {
	font-size: 8em;
	line-height: 0.8em;
}
//#session { margin-top: 20px; }
#bonus { font-size: 2.4em; line-height: 0.7em; padding-top: 5px; }
#bonus .sub { font-size: 0.5em; }
#ip, #logintime, #session, #totaltime {
	color: #FFFFFF;
	width: 49.5%;
}
#ip, #logintime {
	float: left;
	text-align: left;
}
#session, #totaltime {
	float: right;
	text-align: right;
}
#bottom { width: 1010px; margin: auto; }
#onlineheader { text-align: center; font-size: 2em; }
#prev, #next {
	float: left;
	text-align: center;
	width: 100px;
	font-size: 1.25em;
}
#lt, #gt { font-size: 5em; }
#others {
	float: left;
	width: 800px;
	height: 200px;
}
.other {
	float: left;
	width: 20%;
	text-align: center;
}
.holder, .row1, .row2 { width: 800px;}
.holder { height: 200px; }
.row1, .row2 { height: 100px; }
.name { font-size: 1.5em; }
.level { font-size: 4em; }

#color {
	font-size: 1.7em;
	line-height: 0.9em;
}
.colorholder {
	float: left;
	padding: 5px; 
}