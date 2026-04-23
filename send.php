<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. Подключаем библиотеку (проверьте путь к папке!)
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Неверный метод']);
    exit;
}

// 2. Валидация данных
$name = trim(strip_tags($_POST['name'] ?? ''));
$email = trim(strip_tags($_POST['email'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));
$phone = trim(strip_tags($_POST['phone'] ?? ''));

if (empty($name) || empty($email) || empty($message) || !isset($_POST['privacy_agree'])) {
    echo json_encode(['success' => false, 'error' => 'Заполните все поля и дайте согласие']);
    exit;
}

// 3. Настройка PHPMailer
$mail = new PHPMailer(true);

try {
    // Настройки локальной отправки
    $mail->isSMTP();
    $mail->Host       = 'localhost';
    $mail->SMTPAuth   = false; 
    $mail->SMTPAutoTLS = false; 
    $mail->Port       = 25; 
    $mail->CharSet    = 'UTF-8';

    // От кого и кому
    $mail->setFrom('info@gglim.ru', 'Сайт Green Light');
    $mail->addAddress('info@gglim.ru'); // Ваш рабочий email

    // Контент
    $mail->isHTML(false);
    $mail->Subject = "Новое сообщение: $name";
    $mail->Body    = "Имя: $name\nEmail: $email\nТелефон: $phone\nСообщение:\n$message";

    $mail->send();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Если здесь будет ошибка "Connection failed", значит localhost:25 закрыт
    echo json_encode(['success' => false, 'error' => 'Ошибка почтового сервера: ' . $mail->ErrorInfo]);
}
