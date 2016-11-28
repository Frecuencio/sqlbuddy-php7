<?php
/*

SQL Buddy - Web based MySQL administration
http://interruptorgeek.com/sql-buddy-ig-review/

logout.php
- end a session

MIT license

Original : 2008 Calvin Lough <http://calv.in>
Reviewed : 2016 Carlos Martín Arnillas <https://interruptorgeek.com>

*/

if (!session_id())
	session_start();

if (isset($_SESSION['SB_LOGIN'])) {
	$_SESSION['SB_LOGIN'] = null;
	unset($GLOBALS['_SESSION']['SB_LOGIN']);
}

if (isset($_SESSION['SB_LOGIN_STRING'])) {
	$_SESSION['SB_LOGIN_STRING'] = null;
	unset($GLOBALS['_SESSION']['SB_LOGIN_STRING']);
}

if (isset($_SESSION['SB_LOGIN_USER'])) {
	$_SESSION['SB_LOGIN_USER'] = null;
	unset($GLOBALS['_SESSION']['SB_LOGIN_USER']);
}

if (isset($_SESSION['SB_LOGIN_PASS'])) {
	$_SESSION['SB_LOGIN_PASS'] = null;
	unset($GLOBALS['_SESSION']['SB_LOGIN_PASS']);
}

header("Location: login.php");

?>
