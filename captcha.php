<?php
session_start();

// Генерация случайного CAPTCHA
$captcha = '';
$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$characters_length = strlen($characters);
for ($i = 0; $i < 6; $i++) {
    $captcha .= $characters[rand(0, $characters_length - 1)];
}

// Сохранение CAPTCHA в сессии
$_SESSION['captcha'] = $captcha;

// Создание изображения CAPTCHA
$image = imagecreate(100, 30);
$background_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
imagestring($image, 5, 10, 8, $captcha, $text_color);

// Отправка изображения в браузер
header('Content-type: image/png');
imagepng($image );

// Освобождение памяти
imagedestroy($image);
?>