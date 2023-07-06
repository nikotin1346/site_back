<?php
// Проверка CAPTCHA
session_start();
if ($_POST['captcha'] !== $_SESSION['captcha']) {
    die('Неверная CAPTCHA');
}

// Подключение к базе данных MySQL
$db_host = '127.0.0.1';
$db_user = 'root';
$db_password = '';
$db_name = 'bd_praktika';
$db_conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$db_conn) {
    die('Ошибка подключения к базе данных: ' . mysqli_connect_error());
}

// Получение IP пользователя и его браузера
if(!empty($_SERVER['HTTP_CLIENT_IP'])){
  $ip=$_SERVER['HTTP_CLIENT_IP'];
}
elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
}
else{
  $ip=$_SERVER['REMOTE_ADDR'];
}
$browser = $_SERVER['HTTP_USER_AGENT'];

// Защита от SQL-инъекций
$username = mysqli_real_escape_string($db_conn, $_POST['username']);
$email = mysqli_real_escape_string($db_conn, $_POST['email']);
$homepage = mysqli_real_escape_string($db_conn, $_POST['homepage']);
$text = mysqli_real_escape_string($db_conn, $_POST['text']);
$ip = mysqli_real_escape_string($db_conn, $ip);
$browser = mysqli_real_escape_string($db_conn, $browser);

// Вставка сообщения в базу данных с данными об IP и браузере
$query = "INSERT INTO messages (username, email, homepage, text, ip, browser) VALUES ('$username', '$email', '$homepage', '$text', '$ip', '$browser')";

$result = mysqli_query($db_conn, $query);
if (!$result) {
    die('Ошибка запроса: ' . mysqli_error($db_conn));
}

// Закрытие соединения с базой данных
mysqli_close($db_conn);

// Перенаправление на главную страницу
header('Location: index.php');
exit();