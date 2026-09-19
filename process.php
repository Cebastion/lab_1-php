<?php
// process.php - обробка даних форми з index.html

// Підрахунок кількості символів у UTF-8 рядку.
// Замість mb_strlen() (потребує розширення mbstring) рахуємо символи
// регулярним виразом з прапорцем u: кожен збіг - один символ.
function strLength(string $str): int
{
    return preg_match_all("/./us", $str);
}

// Масив для збору повідомлень про помилки
$errors = [];

// Перевіряємо, що дані надійшли методом POST (а не відкрито адресу напряму)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $errors[] = "Дані форми не отримано. Заповніть форму на сторінці index.html.";
    $name = "";
    $surname = "";
} else {
    // Отримуємо значення. Оператор ?? підставляє "", якщо поля немає в запиті.
    // trim() прибирає зайві пробіли на початку та в кінці.
    $name    = $_POST["name"]    ?? "";
    $surname = $_POST["surname"] ?? "";

    // Перевірка типу даних: очікуємо рядки (а не масиви, як при name[]=...)
    if (!is_string($name) || !is_string($surname)) {
        $errors[] = "Отримано некоректний тип даних: очікуються текстові значення.";
        $name = "";
        $surname = "";
    } else {
        $name    = trim($name);
        $surname = trim($surname);

        // Регулярний вираз: лише літери будь-якої мови, пробіл, апостроф і дефіс.
        // Прапорець u потрібен для коректної роботи з кирилицею (UTF-8).
        $pattern = "/^[\p{L}][\p{L}\s'’\-]*$/u";

        // Перевірка імені
        if ($name === "") {
            $errors[] = "Поле «Ім'я» не заповнене.";
        } elseif (!preg_match($pattern, $name)) {
            $errors[] = "Поле «Ім'я» може містити лише літери, пробіл, апостроф та дефіс.";
        } elseif (strLength($name) > 50) {
            $errors[] = "Поле «Ім'я» не може бути довшим за 50 символів.";
        }

        // Перевірка прізвища
        if ($surname === "") {
            $errors[] = "Поле «Прізвище» не заповнене.";
        } elseif (!preg_match($pattern, $surname)) {
            $errors[] = "Поле «Прізвище» може містити лише літери, пробіл, апостроф та дефіс.";
        } elseif (strLength($surname) > 50) {
            $errors[] = "Поле «Прізвище» не може бути довшим за 50 символів.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Результат</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 40px 16px; }
        .box { max-width: 420px; margin: 0 auto; padding: 24px; background: #fff;
               border: 1px solid #d5dbe1; border-radius: 8px; }
        .error { color: #b3261e; }
        .success { color: #1b6e3c; }
        ul { padding-left: 20px; }
        a { color: #2b6cb0; }
    </style>
</head>
<body>
<div class="box">
<?php if (!empty($errors)): ?>
    <h1 class="error">Помилка</h1>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <!-- htmlspecialchars захищає від XSS: спецсимволи перетворюються на безпечні -->
    <h1 class="success">
        Привіт, <?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?>
        <?= htmlspecialchars($surname, ENT_QUOTES, "UTF-8") ?>!
    </h1>
    <p>Дані успішно отримано та перевірено.</p>
<?php endif; ?>
    <p><a href="index.html">Повернутися до форми</a></p>
</div>
</body>
</html>