<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<link rel="stylesheet" href="css/style.css">
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/app.js"></script>
</head>
<body>

<div class="ajaxStatus">saving...</div>

<form id="addbook">
<label>Imię i nazwisko autora:</label>
<input type="text" name="author" />

<label>Tytuł książki:</label>
<input type="text" name="name" />

<label>Opis książki:</label>
<input type="text" name="book_desc" />

<input type="submit" value="Dodaj książkę" />
</form>

<hr/>

<h1>Książki</h1>
<a id="refresh" href="#">Odśwież</a>
<table id="ksiazki" border="1" cellspacing="4" cellpadding="4" width="100%">
<tr>
	<th>Autor</th>
	<th>Tytuł</th>
	<th>Opis</th>
	<th>Edytuj</th>
	<th>Usuń</th>
</tr>
</table>


</body>
</html>
