<pre>
<?php
include_once('db.config.inc.php');
include_once('Book.inc.php');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$mysqli->set_charset('utf8');
echo $mysqli->host_info . "\n";

$res = $mysqli->query("SELECT author, name FROM books ORDER BY author, name");
$books = [];
while ($row = $res->fetch_assoc()) {
	print $row['author'] . ':' . $row['name'] . "\n";
	$books[] = $row;
}

$booksJson = json_encode($books);
var_dump($booksJson);

print "<hr/>";

$book = new Book();

if ( $book->loadFromDB($mysqli, 5) ) {
	var_dump( json_encode($book) );
} else {
	echo "ERROR: (" . $mysqli->errno . ") " . $mysqli->error;
}

print "<hr/>";

// INSERT / DELETE
// if ( $book->create($mysqli, "Potop2", "Henryk Sienkiewicz2") ) {
// 	var_dump( json_encode($book) );
// 	$book->deleteFromDb($mysqli);
// } else {
// 	echo "ERROR: (" . $mysqli->errno . ") " . $mysqli->error;
// }

// UPDATE
if ( $book->update($mysqli, "Pan Wołodyjowski", "Henryk Sienkiewicz") ) {
	var_dump( json_encode($book) );	
} else {
	echo "ERROR: (" . $mysqli->errno . ") " . $mysqli->error;
}
?>
</pre>