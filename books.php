<?php
header('Content-type: application/json');

// books.php
include_once('db.config.inc.php');
include_once('Book.inc.php');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$mysqli->set_charset('utf8');

if ($_SERVER['REQUEST_METHOD']=='GET') {
	// zwracamy wszystkie książki
	$sql_id = "SELECT id FROM books ORDER BY author, name";

	// jesli api wywołane z parametrem book_id, zwracamy dane jednej ksiazki
	if ( @$_GET['book_id']!='' && intval($_GET['book_id'])>0 ) {
		$safe_book_id = $mysqli->real_escape_string($_GET['book_id']);
		$sql_id .= " AND id=$safe_book_id";
	}

	$res = $mysqli->query($sql_id);
	$books = [];
	while ($row = $res->fetch_assoc()) {
		$book = new Book();
		$book->loadFromDB($mysqli, $row['id']);
		$books[] = $book;
	}
	// var_dump($books);
	echo json_encode($books);
} else if ($_SERVER['REQUEST_METHOD']=='PUT') {
	// dodajemy książkę
	parse_str(file_get_contents("php://input"), $put_vars);
	$author = $put_vars['author'];
	$name = $put_vars['name'];
	$book_desc = $put_vars['book_desc'];

	$book = new Book();
	if ( $book->create($mysqli, $author, $name, $book_desc) ) {
		echo json_encode(['status' => 'OK']);
	} else {
		echo json_encode(['status' => 'SAVE ERROR']);
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
	// kasujemy książkę
	// wczytujemy dane ze standardowego wejscia php://input do tablicy $del_vars
	parse_str(file_get_contents("php://input"), $del_vars);

	// tablica $del_vars to tablica asocjacyjna
	$book_id = @$del_vars['book_id'];

	$book = new Book();
	if ( $book->loadFromDB($mysqli, $book_id) ) {
		if ( $book->deleteFromDb($mysqli) ) {
			// udalo sie usunac ksiazke
			echo json_encode(['status' => 'OK']);
		} else {
			// nie udalo sie usunac ksiazki
			echo json_encode(['status' => 'DELETE ERROR']);
		}
	} else {
		// nie udalo sie wczytac ksiazki
		echo json_encode(['status' => 'LOAD ERROR']);
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// edytujemy książkę
	// do przerobienia
	$author = $_POST['author'];
	$name = $_POST['name'];
	$book_desc = $_POST['book_desc'];
	$book_id = $_POST['book_id'];

	$book = new Book();
	if ( $book->loadFromDB($mysqli, $book_id) ) {
		if ( $book->update($mysqli, $name, $author, $book_desc) ) {
			// udalo sie uaktualnic ksiazke
			echo json_encode(['status' => 'OK']);
		} else {
			// nie udalo sie uaktualnic ksiazki
			echo json_encode(['status' => 'UPDATE ERROR']);
		}
	} else {
		// nie udalo sie wczytac ksiazki
		echo json_encode(['status' => 'LOAD ERROR']);
	}
}
?>