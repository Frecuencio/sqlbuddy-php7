<?php
/*

SQL Buddy - Web based MySQL administration
http://interruptorgeek.com/sql-buddy-ig-review/

ajaxsaveedit.php
- saves data to the database

MIT license

Original : 2008 Calvin Lough <http://calv.in>
Reviewed : 2016 Carlos Martín Arnillas <https://interruptorgeek.com>

*/

include "functions.php";

loginCheck();

if (isset($db))
	$conn->selectDB($db);

if ($_POST && isset($table)) {

	$insertChoice = "";

	if (isset($_POST['SB_INSERT_CHOICE'])) {
		$insertChoice = $_POST['SB_INSERT_CHOICE'];
	}

	$structureSql = $conn->describeTable($table);

	while ($structureRow = $conn->fetchAssoc($structureSql)) {
		$pairs[$structureRow['Field']] = '';
		$types[$structureRow['Field']] = $structureRow['Type'];
		$nulls[$structureRow['Field']] = (isset($structureRow['Null'])) ? $structureRow['Null'] : "YES";
	}

	foreach ($_POST as $key=>$value) {
		if ($key != "SB_INSERT_CHOICE") {
			if (is_array($value)) {
				$value = implode(",", $value);
			}

			$pairs[$key] = $conn->escapeString($value);
		}
	}

	if (isset($pairs)) {

		if ($insertChoice != "INSERT") {
			$updates = "";

			foreach ($pairs as $keyname=>$value) {
				if (isset($types) && substr($value, 0, 2) == "0x" && isset($binaryDTs) && in_array($types[$keyname], $binaryDTs)) {
					$updates .= "`" . $keyname . "`=" . $value . ",";
				} else if (!$value && !($value != '' && (int)$value == 0) && $nulls[$keyname] == "YES") {
					$updates .= "`" . $keyname . "`=NULL,";
				} else {
					$updates .= "`" . $keyname . "`='" . $value . "',";
				}
			}

			$updates = substr($updates, 0, -1);

			if (isset($_GET['queryPart'])) {
				$queryPart = $_GET['queryPart'];
			}
			else {
				$queryPart = "";
			}

			$query = "UPDATE `$table` SET " . $updates . " " . $queryPart;

		} else {
			$columns = "";
			$values = "";

			foreach ($pairs as $keyname=>$value) {

				$columns .= "`" . $keyname . "`,";

				if (isset($types) && substr($value, 0, 2) == "0x" && isset($binaryDTs) && in_array($types[$keyname], $binaryDTs)) {
					$values .= $value . ",";
				} else {
					$values .= "'" . $value . "',";
				}

			}

			$columns = substr($columns, 0, -1);
			$values = substr($values, 0, -1);
			$query = "INSERT INTO `$table` ($columns) VALUES ($values)";
		}

		$conn->query($query) or ($dbError = $conn->error());

		echo "{\n";
		echo "    \"formupdate\": \"" . $_GET['form'] . "\",\n";
		echo "    \"errormess\": \"";
		if (isset($dbError))
			echo $dbError;
		echo "\"\n";
		echo '}';

	}
}

?>
