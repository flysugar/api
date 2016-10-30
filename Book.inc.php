<?php
class Book implements JsonSerializable {
	
	private $id;
	private $name;
	private $author;
	private $book_desc;

	public function __construct($name='', $author='', $book_desc='') {
		$this->id 		= -1;
		$this->name 	= $name;
		$this->author 	= $author;
		$this->book_desc= $book_desc;
	}

	public function getId() {
		return $this->id;
	}

	public function getAuthor() {
		return $this->author;
	}

	public function setAuthor($author) {
		$this->author = $author;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getBookDesc() {
		return $this->book_desc;
	}

	public function setBookDesc($book_desc) {
		$this->book_desc = $book_desc;
	}

	public function loadFromDB($conn, $id) {
		$query = "SELECT name, author, book_desc FROM books WHERE id=$id";
		if ( $res = $conn->query($query) ) {
			$row = $res->fetch_assoc();
			$this->name = $row['name'];
			$this->author = $row['author'];
			$this->book_desc = $row['book_desc'];
			$this->id = $id;
			return true;
		} else {
			return false;
		}
	}

	public function create($conn, $author, $name, $book_desc) {
		// przygotowanie wartosci do wprowadzenia do bazy danych
		$safe_name = $conn->real_escape_string($name);
		$safe_author = $conn->real_escape_string($author);
		$safe_book_desc = $conn->real_escape_string($book_desc);

		$query = "INSERT INTO books (name, author, book_desc) VALUES ('$safe_name', '$safe_author', '$book_desc')";
		if ( $res = $conn->query($query) ) {
			$this->name = $name;
			$this->author = $author;
			$this->book_desc = $book_desc;
			$this->id = $conn->insert_id;
			return true;
		} else {
			return false;
		}
	}

	public function update($conn, $name, $author, $book_desc) {
		// przygotowanie wartosci do wprowadzenia do bazy danych
		$safe_name = $conn->real_escape_string($name);
		$safe_author = $conn->real_escape_string($author);
		$safe_id = $conn->real_escape_string($this->id);
		$safe_book_desc = $conn->real_escape_string($book_desc);

		$query = "UPDATE books SET name='$safe_name', author='$safe_author', book_desc='$book_desc' WHERE id=$safe_id";
		if ( $res = $conn->query($query) ) {
			$this->name = $name;
			$this->author = $author;
			$this->book_desc = $book_desc;
			return true;
		} else {
			return false;
		}
	}

	public function deleteFromDb($conn) {
		// przygotowanie wartosci do wprowadzenia do bazy danych
		$safe_id = $conn->real_escape_string($this->id);

		$query = "DELETE FROM books WHERE id=$safe_id";
		if ( $res = $conn->query($query) ) {
			$this->name = '';
			$this->author = '';
			$this->book_desc = '';
			$this->id = -1;
			return true;
		} else {
			return false;
		}
	}

	public function jsonSerialize() {
		return [
			'id'		=> $this->id,
			'name' 		=> $this->name,
			'author'	=> $this->author,
			'book_desc'	=> $this->book_desc
		];
	}
}
?>