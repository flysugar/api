$(document).ready(function() {
	console.log('DOM zaladowany');

	// funkcja ładująca książki z JSON'a
	function loadBooks() {
		$('div.ajaxStatus').toggle();
		$.get('http://localhost/~lukaszf/api/books.php', function(json) {
			// usuwamy poprzednio zaladowane ksiazki
			$('table#ksiazki').find('tr.book').remove();

			var ksiazki = json;
			// console.log(ksiazki);

			for (var i=0; i<ksiazki.length; i++) {
				var ksiazka = $('<tr class="book" data-book-id="'+ ksiazki[i].id +'"><td>' + ksiazki[i].author + '</td><td>' + ksiazki[i].name + '</td><td>' + ksiazki[i].book_desc + '</td><td><a class="edit" href="#">Edytuj</a></td><td><a class="remove" href="#">Usuń</a></td></tr>');
				$('table#ksiazki').append(ksiazka);
			}
			$('div.ajaxStatus').toggle();
		})		
	}

	// dodaje książkę metodą Post
	function updateBook(authorVal, nameVal, book_descVal, book_idVal) {
		$('div.ajaxStatus').toggle();

		// wysylamy dane post-em
		$.post('http://localhost/~lukaszf/api/books.php',
				{author: authorVal, name: nameVal, book_desc: book_descVal, book_id: book_idVal})
		.done(function(json) {
			console.log(json);

			// usuwamy formularze do edycji ksiazek
			$('table#ksiazki').find('tr.editForm').remove();

			// jesli sie udalo, ladujemy uaktualniona liste ksiazek
			loadBooks();
		})
		.fail(function(xhr) {
			console.log('update error', xhr);
		})
		.always(function(xhr) {
			// chowamy loading...
			$('div.ajaxStatus').toggle();
		})
	}

	// dodaje książkę metodą PUT
	function addBook(authorVal, nameVal, book_descVal) {
		$('div.ajaxStatus').toggle();

		// wysylamy dane post-em
		$.ajax({
			url: 'http://localhost/~lukaszf/api/books.php',
			data: {author: authorVal, name: nameVal, book_desc: book_descVal},
			type: 'PUT',
			dataType: 'json'
		})
		.done(function(json) {
			// jesli sie udalo, ladujemy uaktualniona liste ksiazek
			console.log(json);

			// czyscimy pola po udanym dodaniu
			$('form#addbook').find('input[type="text"]').prop('value', '');

			loadBooks();
		})
		.fail(function(xhr) {
			console.log('add error', xhr);
		})
		.always(function(xhr) {
			// chowamy loading...
			$('div.ajaxStatus').toggle();
		})
	}

	function removeBook(bookId) {
		$('div.ajaxStatus').toggle();

		$.ajax({
			url: 'http://localhost/~lukaszf/api/books.php',
			data: {book_id: bookId},
			type: 'DELETE',
			dataType: 'json'
		})
		.done(function(json) {
			// jesli sie udalo, ladujemy uaktualniona liste ksiazek
			console.log(json);
			loadBooks();
		})
		.fail(function(xhr) {
			console.log('add error', xhr);
		})
		.always(function(xhr) {
			// chowamy loading...
			$('div.ajaxStatus').toggle();
		})
	}

	function editBook(bookId) {
		var author = $('table#ksiazki').find('tr.book[data-book-id="'+bookId+'"] td').eq(0).text();
		var name = $('table#ksiazki').find('tr.book[data-book-id="'+bookId+'"] td').eq(1).text();
		var book_desc = $('table#ksiazki').find('tr.book[data-book-id="'+bookId+'"] td').eq(2).text();
		var editForm = $('<tr class="editForm" data-book-id="'+bookId+'"><td><input name="author" value="'+author+'" /></td><td><input name="name" value="'+name+'"/></td><td><input name="book_desc" value="'+book_desc+'" /></td><td><a class="save" href="#">Zapisz</a></td><td><a class="cancelSave" href="#">Anuluj</a></td></tr>');
		$('table#ksiazki').find('tr.book[data-book-id="'+bookId+'"]').after(editForm);

		// ustawia focus na pierwszym polu formularza
		$('table#ksiazki').find('tr.editForm[data-book-id="'+bookId+'"]').find('input[name="author"]').focus();
	}

	// przy pierwszym wyświetleniu strony ładujemy wszystkie książki
	loadBooks();

	// zdarzenie dla przycisku Odśwież
	$('a#refresh').click(function() {
		loadBooks();
	})

	// zdarzenie przy kliknięciu w link Zapisz
	// dopinane dynamicznie, również dla nowych elementów dodawanych przez append
	$('table#ksiazki').on('click', 'a.save', function() {
		var author = $(this).parents('tr.editForm').find('input[name="author"]').prop('value');
		var name = $(this).parents('tr.editForm').find('input[name="name"]').prop('value');
		var book_desc = $(this).parents('tr.editForm').find('input[name="book_desc"]').prop('value');
		var bookId = $(this).parents('tr.editForm').data('book-id');

		updateBook(author, name, book_desc, bookId);
		// tutaj należy dodać funkcję do zapisu danych metodą POST
	})

	// uruchamianie zapisu przy kliknieciu Enter (kod znaku - 13)
	$('table#ksiazki').on('keypress', 'tr.editForm input', function(e) {
		if (e.which == 13) {
			var author = $(this).parents('tr.editForm').find('input[name="author"]').prop('value');
			var name = $(this).parents('tr.editForm').find('input[name="name"]').prop('value');
			var book_desc = $(this).parents('tr.editForm').find('input[name="book_desc"]').prop('value');
			var bookId = $(this).parents('tr.editForm').data('book-id');

			updateBook(author, name, book_desc, bookId);
			// tutaj należy dodać funkcję do zapisu danych metodą POST
		}
	})

	// zdarzenie przy kliknięciu w link Anuluj
	// dopinane dynamicznie, również dla nowych elementów dodawanych przez append
	$('table#ksiazki').on('click', 'a.cancelSave', function() {
		$(this).parents('tr.editForm').remove();
	})

	// zdarzenie przy kliknięciu w link Edytuj
	// dopinane dynamicznie, również dla nowych elementów dodawanych przez append
	$('table#ksiazki').on('click', 'a.edit', function() {
		// stosujemy parents (ew. parent().parent()) aby wyjsc najpierw z TD, a potem dojsc do TR
		// id ksiazki zapisane jest w dataset data-book-id="x" w elemencie TR dla każdej książki
		var bookId = $(this).parents('tr.book').first().data('book-id');
		// alert('EDIT:' + bookId);
		editBook(bookId);
	})

	// zdarzenie przy kliknięciu w link Edytuj
	// dopinane dynamicznie, również dla nowych elementów dodawanych przez append
	$('table#ksiazki').on('click', 'a.remove', function() {
		var bookId = $(this).parents('tr.book').first().data('book-id');
		if ( confirm('Czy na pewno chcesz usunąć książkę (id:' + bookId + ')') ) {
			removeBook(bookId);
		}
	})

	$('form#addbook').find('input[type="submit"]').click(function() {
		event.preventDefault();
		var author = $(this).parent().find('input[name="author"]').prop('value');
		var name = $(this).parent().find('input[name="name"]').prop('value');
		var book_desc = $(this).parent().find('input[name="book_desc"]').prop('value');

		console.log(author + name + book_desc);
		addBook(author, name, book_desc);
	})


	// var ksiazka = $('<tr><td>author</td><td>name</td><td>book_desc</td></tr>');
	// $('table#ksiazki').append(ksiazka);

	// $.ajax({
	// 	url: 'http://date.jsontest.com/',
	// 	data: {},
	// 	type: 'GET',
	// 	dataType: 'json',
	// 	success: function(json) {
	// 		console.log('sukces!', json);
	// 	},
	// 	error: function(xhr, status, error) {
	// 		console.log('blad', error);
	// 	},
	// 	complete: function(xhr, status) {
	// 		console.log('wykonano zapytanie', xhr);
	// 	}
	// });

	// $.get('http://date.jsontest.com/', function(json) {
	// 	// $('div#json').text(json);
	// 	console.log(json);

	// 	var czas = $('<p>Czas: ' + json.time + '</p>');
	// 	var timestamp = $('<p>Timestamp: ' + json.milliseconds_since_epoch + '</p>');
	// 	var data = $('<p>Data: ' + json.date + '</p>');

	// 	$('div#json').prepend(czas);
	// 	$('div#json').prepend(timestamp);
	// 	$('div#json').prepend(data);
	// })

})