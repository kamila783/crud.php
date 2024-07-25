<?php
$errors = []; // Массив для хранения ошибок при валидации данных

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Валидация полей формы
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Фильтрация и очистка имени пользователя
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL); // Валидация email
    $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT); // Валидация возраста

    // Проверка обязательных полей
    if (!$username) {
        $errors[] = "Username is required."; // Ошибка, если имя пользователя не указано или содержит недопустимые символы
    }
    if (!$email) {
        $errors[] = "Valid email is required."; // Ошибка, если email не прошел валидацию
    }
    if ($age === false || $age === null) {
        $errors[] = "Valid age is required."; // Ошибка, если возраст не является целым числом или отсутствует
    }

    // Проверка загрузки фотографии
    $photoPath = ''; // Переменная для хранения пути к загруженной фотографии
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Проверка и сохранение загруженной фотографии
        $uploadDir = 'uploads/'; // Директория для сохранения фотографий
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Создаем директорию, если она не существует
        }
        $uploadFile = $uploadDir . uniqid() . '_' . basename($_FILES['photo']['name']); // Уникальное имя файла
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            $photoPath = $uploadFile; // Сохраняем путь к загруженной фотографии
        } else {
            $errors[] = "Failed to upload photo."; // Ошибка, если не удалось загрузить фотографию
        }
    } elseif ($_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "Error uploading photo: " . $_FILES['photo']['error']; // Ошибка при загрузке фотографии
    }

    // Если нет ошибок, добавляем нового пользователя
    if (empty($errors)) {
        // Загрузка существующих пользователей из JSON, если файл существует
        $users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];

        // Создание нового пользователя
        $newUser = [
            'id' => uniqid(), // Уникальный идентификатор пользователя
            'username' => $username, // Имя пользователя
            'email' => $email, // Email пользователя
            'age' => $age, // Возраст пользователя
            'photoPath' => $photoPath // Путь к загруженной фотографии
        ];

        $users[] = $newUser; // Добавление нового пользователя в массив пользователей

        // Сохранение обновленного списка пользователей в JSON файл
        file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));

        // Перенаправление на главную страницу после успешного добавления пользователя
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
</head>
<body>
    <h2>Create User</h2>

    <!-- Отображение ошибок валидации, если они есть -->
    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Форма для создания нового пользователя -->
    <form action="create.php" method="POST" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required>
        <br>
        <label for="photo">Photo:</label>
        <input type="file" id="photo" name="photo">
        <br>
        <button type="submit">Add User</button>
    </form>
    <hr>
    <a href="index.php">Back to User List</a> <!-- Ссылка для возврата на главную страницу -->
</body>
</html>


