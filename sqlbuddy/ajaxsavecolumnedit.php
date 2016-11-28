<?php
/*

SQL Buddy - Web based MySQL administration
http://interruptorgeek.com/sql-buddy-ig-review/

ajaxsavecolumnedit.php
- saves the details of a table column

MIT license

Original : 2008 Calvin Lough <http://calv.in>
Reviewed : 2016 Carlos Martín Arnillas <https://interruptorgeek.com>

*/

include "functions.php";

loginCheck();

if (isset($db))
	$conn->selectDB($db);

if (isset($_POST['runQuery'])) {
	$query = $_POST['runQuery'];

	$conn->query($query) or ($dbError = $conn->error());

	echo "{\n";
	echo "    \"formupdate\": \"" . $_GET['form'] . "\",\n";
	echo "    \"errormess\": \"";
	if (isset($dbError))
		echo $dbError;
	echo "\"\n";
	echo '}';

}

?>
