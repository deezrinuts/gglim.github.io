<?php
// 1. ВКЛЮЧАЕМ ОШИБКИ ДЛЯ ТЕСТА
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 2. ПУТИ К ФАЙЛАМ (Проверьте, что папка src внутри phpmailer)
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

header('Content-Type: application/json; charset=utf-8');

$mail = new PHPMailer(true);

try {
    // Настройки для локального сервера (раз MX ведет на email.gglim.ru)
    $mail->isSMTP();
    $mail->Host       = 'localhost'; 
    $mail->SMTPAuth   = false;
    $mail->Port       = 25;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('info@gglim.ru', 'Green Light Website');
    $mail->addAddress('info@gglim.ru');

    $mail->isHTML(true);
    $mail->Subject = 'Новая заявка с сайта';
    $mail->Body    = "Имя: " . $_POST['name'] . "<br>Телефон: " . $_POST['phone'];

    $mail->send();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
}
