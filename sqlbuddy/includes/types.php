<?php
/*

SQL Buddy - Web based MySQL administration
http://interruptorgeek.com/sql-buddy-ig-review/

types.php
- list of data types

MIT-style license

Original : Calvin Lough, <http://calv.in>
Reviewed : 2016 Carlos Martín Arnillas <https://interruptorgeek.com>

*/

$typeList[] = "varchar";
$typeList[] = "char";
$typeList[] = "text";
$typeList[] = "tinytext";
$typeList[] = "mediumtext";
$typeList[] = "longtext";
$typeList[] = "tinyint";
$typeList[] = "smallint";
$typeList[] = "mediumint";
$typeList[] = "int";
$typeList[] = "bigint";
$typeList[] = "real";
$typeList[] = "double";
$typeList[] = "float";
$typeList[] = "decimal";
$typeList[] = "numeric";
$typeList[] = "date";
$typeList[] = "time";
$typeList[] = "datetime";
$typeList[] = "timestamp";
$typeList[] = "tinyblob";
$typeList[] = "blob";
$typeList[] = "mediumblob";
$typeList[] = "longblob";
$typeList[] = "binary";
$typeList[] = "varbinary";
$typeList[] = "bit";
$typeList[] = "enum";
$typeList[] = "set";

if(isset($_SESSION['MYSQL_VERSION']) && $_SESSION['MYSQL_VERSION'] >= 5.7) {
  $typeList[] = "json";

  $typeList[] = "geometry";
  $typeList[] = "point";
  $typeList[] = "linestring";
  $typeList[] = "polygon";
  $typeList[] = "multipoint";
  $typeList[] = "multilinestring";
  $typeList[] = "multipolygon";
  $typeList[] = "geometrycollection";
}

$textDTs[] = "text";
$textDTs[] = "mediumtext";
$textDTs[] = "longtext";

$numericDTs[] = "tinyint";
$numericDTs[] = "smallint";
$numericDTs[] = "mediumint";
$numericDTs[] = "int";
$numericDTs[] = "bigint";
$numericDTs[] = "real";
$numericDTs[] = "double";
$numericDTs[] = "float";
$numericDTs[] = "decimal";
$numericDTs[] = "numeric";

$binaryDTs[] = "tinyblob";
$binaryDTs[] = "blob";
$binaryDTs[] = "mediumblob";
$binaryDTs[] = "longblob";
$binaryDTs[] = "binary";
$binaryDTs[] = "varbinary";

?>
