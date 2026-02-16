<?php
// send.php — обработчик формы обратной связи
header('Content-Type: application/json; charset=utf-8');

// Настройки
$to = "ваш-email@домен.ru"; // ИЗМЕНИТЕ НА СВОЙ АДРЕС
$subject = "Новое сообщение с сайта Green Light";

// 1. Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Неверный метод запроса']);
    exit;
}

// 2. Honeypot — защита от ботов (если поле заполнено — это бот)
if (!empty($_POST['website'])) {
    // Бот — тихо выходим
    echo json_encode(['success' => false, 'error' => 'Обнаружен спам-бот']);
    exit;
}

// 3. Получаем и чистим данные
$name = trim(strip_tags($_POST['name'] ?? ''));
$email = trim(strip_tags($_POST['email'] ?? ''));
$phone = trim(strip_tags($_POST['phone'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

// 4. Валидация
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Укажите имя (минимум 2 символа)';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Укажите корректный email';
}

if (empty($message) || strlen($message) < 5) {
    $errors[] = 'Напишите сообщение (минимум 5 символов)';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'error' => implode('. ', $errors)]);
    exit;
}

// 5. Формируем тело письма
$body = "Имя: $name\n";
$body .= "Email: $email\n";
if (!empty($phone)) {
    $body .= "Телефон: $phone\n";
}
$body .= "Сообщение:\n$message\n";

$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// 6. Отправка письма
if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'error' => '']);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка при отправке письма. Попробуйте позже.']);
}