<?php
/*

SQL Buddy - Web based MySQL administration
http://interruptorgeek.com/sql-buddy-ig-review/

ajaxcreatetable.php
- called from dboverview.php to create a new table

MIT license

Original : 2008 Calvin Lough <http://calv.in>
Reviewed : 2016 Carlos Martín Arnillas <https://interruptorgeek.com>

*/

include "functions.php";

loginCheck();

if (isset($db))
	$conn->selectDB($db);

if (isset($_POST['query'])) {

	$queryList = splitQueryText($_POST['query']);

	foreach ($queryList as $query) {
		$sql = $conn->query($query) or ($dbError = $conn->error());
	}

	if (isset($dbError)) {
		echo $dbError;
	}

}

?>
