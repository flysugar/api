<?php
// books.php
include_once('db.config.inc.php');
include_once('Book.inc.php');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$mysqli->set_charset('utf8');


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	echo ("GET<br>");
	// zwraca liste ksiazek
	var_dump($_GET);
} else if ($_SERVER['REQUEST_METHOD' ] == 'PUT' ) {
	echo("PUT<br>");
	parse_str(file_get_contents("php://input"), $put_vars);
	var_dump($put_vars);
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
	echo ("DELETE");
	parse_str(file_get_contents("php://input"), $del_vars);
	var_dump($del_vars);
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	echo ("POST<br>");
	var_dump($_POST);
}
?>