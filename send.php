<?php
// 1. Загрузка конфигурации
$config = require 'config.php';

// 2. Включаем отображение ошибок для отладки (выключите перед продакшеном)
ini_set('display_errors', 0); // Ставим 0, чтобы не портить JSON-ответ
error_reporting(E_ALL);

// 3. Подключение PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

header('Content-Type: application/json; charset=utf-8');

// Проверка метода
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Неверный метод запроса']);
    exit;
}

$mail = new PHPMailer(true);

try {
    // Настройки сервера
    $mail->isSMTP();
    $mail->Host       = $config['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['smtp_user'];
    $mail->Password   = $config['smtp_pass'];
    
    // Для порта 587 используем STARTTLS
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = $config['smtp_port'];

    // Критично для Microsoft Exchange с самоподписанными сертификатами
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->CharSet = 'UTF-8';

    // Получатели
    $mail->setFrom($config['smtp_user'], 'Green Light Website');
    $mail->addAddress($config['smtp_user']); // Отправляем сами себе

    // Контент
    $name  = trim(strip_tags($_POST['name'] ?? 'Не указано'));
    $phone = trim(strip_tags($_POST['phone'] ?? 'Не указано'));

    $mail->isHTML(true);
    $mail->Subject = "Новая заявка: $name";
    $mail->Body    = "<b>Имя:</b> $name <br><b>Телефон:</b> $phone";

    $mail->send();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Возвращаем текст ошибки из PHPMailer
    echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
}
