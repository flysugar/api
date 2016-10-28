<?php
class Book implements JsonSerializable {
	
	private $id;
	private $name;
	private $author;

	public function __construct($name='', $author='') {
		$this->id 		= -1;
		$this->name 	= $name;
		$this->author 	= $author;
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

	public function loadFromDB($conn, $id) {
		$query = "SELECT name, author FROM books WHERE id=$id";
		if ( $res = $conn->query($query) ) {
			$row = $res->fetch_assoc();
			$this->name = $row['name'];
			$this->author = $row['author'];
			$this->id = $id;
			return true;
		} else {
			return false;
		}
	}

	public function create($conn, $name, $author) {
		// przygotowanie wartosci do wprowadzenia do bazy danych
		$safe_name = $conn->real_escape_string($name);
		$safe_author = $conn->real_escape_string($author);

		$query = "INSERT INTO books (name, author) VALUES ('$safe_name', '$safe_author')";
		if ( $res = $conn->query($query) ) {
			$this->name = $name;
			$this->author = $author;
			$this->id = $conn->insert_id;
			return true;
		} else {
			return false;
		}
	}

	public function update($conn, $name, $author) {
		// przygotowanie wartosci do wprowadzenia do bazy danych
		$safe_name = $conn->real_escape_string($name);
		$safe_author = $conn->real_escape_string($author);
		$safe_id = $conn->real_escape_string($this->id);

		$query = "UPDATE books SET name='$safe_name', author='$safe_author' WHERE id=$safe_id";
		if ( $res = $conn->query($query) ) {
			$this->name = $name;
			$this->author = $author;
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
			'author'	=> $this->author
		];
	}

}
?>