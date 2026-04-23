<?php
// 1. Загрузка конфигурации и библиотек
$config = require 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Убираем вывод системных ошибок, чтобы не ломать JSON
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Неверный метод запроса']);
    exit;
}

$mail = new PHPMailer(true);

try {
    // --- Настройки сервера (SMTP) ---
    $mail->isSMTP();
    $mail->Host       = $config['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['smtp_user']; // Здесь GGLIM\info
    $mail->Password   = $config['smtp_pass'];
    
    // Используем STARTTLS для порта 587 (стандарт Exchange)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = $config['smtp_port'];

    // Настройки для работы с самоподписанными сертификатами Exchange
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->CharSet = 'UTF-8';

    // --- Настройки отправителя и получателя ---
    // ВАЖНО: Тут должен быть валидный email, а не GGLIM\info
    $mail->setFrom('info@gglim.ru', 'Green Light Website');
    $mail->addAddress('info@gglim.ru'); 

    // --- Обработка данных формы ---
    $name  = trim(strip_tags($_POST['name'] ?? 'Не указано'));
    $phone = trim(strip_tags($_POST['phone'] ?? 'Не указано'));
    $msg   = trim(strip_tags($_POST['message'] ?? ''));

    // --- Контент письма ---
    $mail->isHTML(true);
    $mail->Subject = "Заявка с сайта: $name";
    $mail->Body    = "
        <h3>Новая заявка</h3>
        <p><b>Имя:</b> $name</p>
        <p><b>Телефон:</b> $phone</p>
        <p><b>Сообщение:</b><br>$msg</p>
    ";

    $mail->send();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // В случае ошибки возвращаем причину
    echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
}
