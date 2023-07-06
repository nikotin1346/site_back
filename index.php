<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Гостевая книга</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Гостевая книга</h1>
    
    <form method="post" action="add_message.php">
        <label for="username">User Name:</label>
        <input type="text" id="username" name="username" required pattern="[a-zA-Z0-9]+" title="Только цифры и буквы латинского алфавита"><br>
        
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required><br>
        
        <label for="homepage">Homepage:</label>
        <input type="url" id="homepage" name="homepage"><br>
        
        <label for="captcha">CAPTCHA:</label>
        <input type="text" id="captcha" name="captcha" required pattern="[a-zA-Z0-9]+" title="Только цифры и буквы латинского алфавита"><br>
        <img src="captcha.php" alt="CAPTCHA"><br>
        
        <label for="text">Text:</label>
        <textarea id="text" name="text" required></textarea><br>
        
        <input type="submit" value="Добавить">
    </form>
    
    <h2>Сообщения</h2>
    <?php
	// Подключение к базе данных MySQL
	$db_host = '127.0.0.1';
	$db_user = 'root';
	$db_password = '';
	$db_name = 'bd_praktika';
	$db_conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
	if (!$db_conn) {
		die('Ошибка подключения к базе данных: ' . mysqli_connect_error());
	}

	// Получение параметров сортировки
	$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'date_added';
	$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

	// Запрос для получения общего количества сообщений
	$query_count = 'SELECT COUNT(*) AS total FROM messages';
	$result_count = mysqli_query($db_conn, $query_count);
	if (!$result_count) {
		die('Ошибка запроса: ' . mysqli_error($db_conn));
	}
	$row_count = mysqli_fetch_assoc($result_count);
	$total_messages = $row_count['total'];

	// Параметры разбиения на страницы
	$messages_per_page = 25;
	$total_pages = ceil($total_messages / $messages_per_page);

	// Получение текущей страницы
	$current_page = isset($_GET['page']) ? max(1, min($_GET['page'], $total_pages)) : 1;

	// Вычисление смещения для LIMIT
	$offset = ($current_page - 1) * $messages_per_page;

	// Запрос для получения сообщений с сортировкой и разбиением на страницы
	$query = "SELECT * FROM messages ORDER BY $sort_field $sort_order LIMIT $offset, $messages_per_page";
	$result = mysqli_query($db_conn, $query);
	if (!$result) {
		die('Ошибка запроса: ' . mysqli_error($db_conn));
	}

	// Вывод сообщений
	echo '<table>';
	echo '<tr><th><a href="?sort=username&order=' . ($sort_field === 'username' && $sort_order === 'ASC' ? 'desc' : 'asc') . '">User Name</a></th><th><a href="?sort=email&order=' . ($sort_field === 'email' && $sort_order === 'ASC' ? 'desc' : 'asc') . '">E-mail</a></th><th><a href="?sort=date_added&order=' . ($sort_field === 'date_added' && $sort_order === 'ASC' ? 'desc' : 'asc') . '">Дата добавления</a></th><th>Homepage</a></th><th>Text</th></tr>';

	while ($row = mysqli_fetch_assoc($result)) {
		echo '<tr>';
		echo '<td>' . htmlspecialchars($row['username']) . '</td>';
		echo '<td>' . htmlspecialchars($row['email']) . '</td>';
		echo '<td>' . htmlspecialchars($row['date_added']) . '</td>';
		echo '<td>' . htmlspecialchars($row['homepage']) . '</td>';
		echo '<td>' . htmlspecialchars($row['text']) . '</td>'; // Добавлено поле "Text"
		echo '</tr>';
	}

	echo '</table>';

	// Вывод пагинации
	echo '<div class="pagination">';
	echo '<span>Страница ' . $current_page . ' из ' . $total_pages . '</span>';
	if ($current_page > 1) {
		echo '<a href="?page=' . ($current_page - 1) . '&sort=' . $sort_field . '&order=' . $sort_order . '">Предыдущая</a>';
	}
	if ($current_page < $total_pages) {
		echo '<a href="?page=' . ($current_page + 1) . '&sort=' . $sort_field . '&order=' . $sort_order . '">Следующая</a>';
	}
	echo '</div>';

	// Закрытие соединения с базой данных
	mysqli_close($db_conn);
	?>
</body>
</html>