<?php
/*

SQL Buddy - Web based MySQL administration
http://interruptorgeek.com/sql-buddy-ig-review/

sql.php
- sql class

MIT license

Original : 2008 Calvin Lough <http://calv.in>
Reviewed : 2016 Carlos Martín Arnillas <https://interruptorgeek.com>
*/

class SQL {

	var $version = "";
	var $conn = "";
	var $options = "";
	var $errorMessage = "";
	var $db = "";

	function __construct($connString, $user = "", $pass = "") {
		list($this->adapter, $options) = explode(":", $connString, 2);

		$optionsList = explode(";", $options);

		foreach ($optionsList as $option) {
			list($a, $b) = explode("=", $option);
			$opt[$a] = $b;
		}

		$this->options = $opt;
		$database = (array_key_exists("database", $opt)) ? $opt['database'] : "";

		$host = (array_key_exists("host", $opt)) ? $opt['host'] : "";
		$this->conn = @mysqli_connect($host, $user, $pass);

		$this->query("SET NAMES 'utf8'");
		$_SESSION['MYSQL_VERSION'] =  mysqli_get_server_version($this->conn);

		if ($this->conn && $database) {
			$this->db = $database;
		}
	}

	function isConnected() {
		return ($this->conn !== false);
	}

	function disconnect() {
		if ($this->conn) {
			mysqli_close($this->conn);
			$this->conn = null;
		}
	}


	function getOptionValue($optKey) {
		if (array_key_exists($optKey, $this->options)) {
			return $this->options[$optKey];
		} else {
			return false;
		}
	}

	function selectDB($db) {
		if ($this->conn) {
			$this->db = $db;
			return (mysqli_select_db($this->conn,$db));
		}
		return false;
	}

	function query($queryText) {
		if ($this->conn) {
			$queryResult = @mysqli_query($this->conn, $queryText);

			if (!$queryResult) {
				$this->errorMessage = mysqli_error($this->conn);
			}

			return $queryResult;
		}
		return false;
	}

	function rowCount($resultSet) {
		if (!$resultSet) {
			return false;
		}

		if ($this->conn) {
			return @mysqli_num_rows($resultSet);
		}
		return false;
	}

	function isResultSet($resultSet) {
		if ($this->conn) {
			return ($this->rowCount($resultSet) > 0);
		}
		return false;
	}

	function fetchArray($resultSet) {
		if (!$resultSet) {
			return false;
		}

		if ($this->conn) {
				return mysqli_fetch_row($resultSet);
		}
		return false;
	}

	function fetchAssoc($resultSet) {
		if (!$resultSet) {
			return false;
		}

		if ($this->conn) {
			return mysqli_fetch_assoc($resultSet);
		}
		return false;
	}

	function affectedRows($resultSet) {
		if (!$resultSet) {
			return false;
		}

		if ($this->conn) {
			return @mysqli_affected_rows($resultSet);
		}
		return false;
	}

	function result($resultSet, $targetRow, $targetColumn = "") {
		if (!$resultSet) {
			return false;
		}
		if ($this->conn) {
			return $this->mysqli_result($resultSet, $targetRow, $targetColumn);
      return $row[$field];
		}
		return false;
	}

	function listDatabases() {
		if ($this->conn) {
			return $this->query("SHOW DATABASES");
		}
	}

	function listTables() {
		if ($this->conn) {
			return $this->query("SHOW TABLES");
		}
	}

	function hasCharsetSupport()
	{
		return $this->conn && version_compare($this->getVersion(), "4.1", ">");
	}

	function listCharset() {
		if ($this->conn) {
			return $this->query("SHOW CHARACTER SET");
		}
		return '';
	}

	function listCollation() {
		if ($this->conn) {
			return $this->query("SHOW COLLATION");
		}
		return '';
	}

	function listEngines() {
		if ($this->conn) {
			return $this->query("SHOW ENGINES");
		}
		return '';
	}

	function insertId() {
		if ($this->conn) {
			return @mysqli_insert_id($this->conn);
		}
		return '';
	}

	function escapeString($toEscape) {
		if ($this->conn) {
			return mysqli_real_escape_string($this->conn ,$toEscape);
		}
		return '';
	}

	function getVersion() {
		if ($this->conn) {
			// cache
			if ($this->version) {
				return $this->version;
			}
			$verSql = mysqli_get_server_info($this->conn);
			$version = explode("-", $verSql);
			$this->version = $version[0];
			return $this->version;
		}
		return '';
	}

	// returns the number of rows in a table
	function tableRowCount($table) {
		if ($this->conn) {
			$countSql = $this->query("SELECT COUNT(*) AS `RowCount` FROM `" . $table . "`");
			$count = (int)($this->result($countSql, 0, "RowCount"));
			return $count;
		}
		return '';
	}

	// gets column info for a table
	function describeTable($table) {
		if ($this->conn) {
			return $this->query("DESCRIBE `" . $table . "`");
		}
		return '';
	}

	/*
		Return names, row counts etc for every database, table and view in a JSON string
	*/
	function getMetadata() {
		$output = '';
		if ($this->conn) {
			if (version_compare($this->getVersion(), "5.0.0", ">=")) {
				$this->selectDB("information_schema");
				$schemaSql = $this->query("SELECT `SCHEMA_NAME` FROM `SCHEMATA` ORDER BY `SCHEMA_NAME`");
				if ($this->rowCount($schemaSql)) {
					while ($schema = $this->fetchAssoc($schemaSql)) {
						$output .= '{"name": "' . $schema['SCHEMA_NAME'] . '"';
						// other interesting columns: TABLE_TYPE, ENGINE, TABLE_COLUMN and many more
						$tableSql = $this->query("SELECT `TABLE_NAME`, `TABLE_ROWS` FROM `TABLES` WHERE `TABLE_SCHEMA`='" . $schema['SCHEMA_NAME'] . "' ORDER BY `TABLE_NAME`");
						if ($this->rowCount($tableSql)) {
							$output .= ',"items": [';
							while ($table = $this->fetchAssoc($tableSql)) {

								if ($schema['SCHEMA_NAME'] == "information_schema") {
									$countSql = $this->query("SELECT COUNT(*) AS `RowCount` FROM `" . $table['TABLE_NAME'] . "`");
									$rowCount = (int)($this->result($countSql, 0, "RowCount"));
								} else {
									$rowCount = (int)($table['TABLE_ROWS']);
								}

								$output .= '{"name":"' . $table['TABLE_NAME'] . '","rowcount":' . $rowCount . '},';
							}

							if (substr($output, -1) == ",")
								$output = substr($output, 0, -1);

							$output .= ']';
						}
						$output .= '},';
					}
					$output = substr($output, 0, -1);
				}
			} else {
				$schemaSql = $this->listDatabases();

				if ($this->rowCount($schemaSql)) {
					while ($schema = $this->fetchArray($schemaSql)) {
						$output .= '{"name": "' . $schema[0] . '"';

						$this->selectDB($schema[0]);
						$tableSql = $this->listTables();

						if ($this->rowCount($tableSql)) {
							$output .= ',"items": [';
							while ($table = $this->fetchArray($tableSql)) {
								$countSql = $this->query("SELECT COUNT(*) AS `RowCount` FROM `" . $table[0] . "`");
								$rowCount = (int)($this->result($countSql, 0, "RowCount"));
								$output .= '{"name":"' . $table[0] . '","rowcount":' . $rowCount . '},';
							}

							if (substr($output, -1) == ",")
								$output = substr($output, 0, -1);

							$output .= ']';
						}
						$output .= '},';
					}
					$output = substr($output, 0, -1);
				}
			}
		}
		return $output;
	}

	function error() {
		return $this->errorMessage;
	}

	protected function mysqli_result($res,$row=0,$col=0){
	    $numrows = mysqli_num_rows($res);
	    if ($numrows && $row <= ($numrows-1) && $row >=0){
	        mysqli_data_seek($res,$row);
	        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
	        if (isset($resrow[$col])){
	            return $resrow[$col];
	        }
	    }
	    return false;
	}

}
