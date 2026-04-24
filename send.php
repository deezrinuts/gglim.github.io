<?php
$config = require 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Неверный метод запроса']);
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $config['smtp_host'];
    $mail->SMTPAuth   = true;
    
    // Авторизация (используем домен\юзер из конфига)
    $mail->Username   = $config['smtp_user']; 
    $mail->Password   = $config['smtp_pass'];
    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = $config['smtp_port'];

    // Настройки для Exchange (игнорируем проблемы с сертификатами .local)
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->CharSet = 'UTF-8';

    // ВАЖНО: setFrom должен быть валидным EMAIL, а не ЛОГИНОМ
    // Если ваш ящик info@gglim.ru, пишем его здесь вручную
    $mail->setFrom('info@gglim.ru', 'Green Light Website');
    $mail->addAddress('info@gglim.ru'); 

    // Данные из формы
    $name  = trim(strip_tags($_POST['name'] ?? 'Не указано'));
    $phone = trim(strip_tags($_POST['phone'] ?? 'Не указано'));
    $msg   = trim(strip_tags($_POST['message'] ?? ''));

    $mail->isHTML(true);
    $mail->Subject = "Заявка с сайта: $name";
    $mail->Body    = "<b>Имя:</b> $name <br><b>Телефон:</b> $phone <br><b>Сообщение:</b> $msg";

    $mail->send();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Если ошибка аутентификации, здесь будет текст от сервера
    echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
}
