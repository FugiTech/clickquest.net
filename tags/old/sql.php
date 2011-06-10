<html>
<head>
<title>Users Table</title>
<style>
table {
	border-collapse: true;
}
td {
	border: 1px solid black;
}
</style>
</head>
<body>
<table><?php

$db = sqlite_factory('clickquest.sql');
$result = $db->query('SELECT * FROM users')->fetchAll(SQLITE_ASSOC);
echo '<tr style="font-weight: bold">';
foreach($db->fetchColumnTypes('users', SQLITE_ASSOC) as $col=>$type) {
	echo '<td>'.$col.' ('.$type.')</td>';
}
echo '</tr>';
foreach($result as $array) {
	echo '<tr>';
	foreach($array as $key=>$value) {
		echo '<td>'.($key=="password" ? md5($value) : $value).'</td>';
	}
	echo '</tr>';
}
?></table>
</body>
</html>