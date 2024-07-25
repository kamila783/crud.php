<?php
// Загрузка данных пользователей из JSON
$users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];

// Определение пути к папке с загруженными фотографиями
$uploadDir = 'uploads/';

// Функция для вывода списка пользователей
function renderUsers($users, $uploadDir)
{
    echo '<h2>User List</h2>'; // Заголовок списка пользователей
    echo '<ul>'; // Начало списка
    foreach ($users as $user) {
        echo '<li>'; // Начало элемента списка
        echo '<strong>Username:</strong> ' . htmlspecialchars($user['username']) . ' | '; // Вывод имени пользователя с экранированием HTML-сущностей
        echo '<strong>Email:</strong> ' . htmlspecialchars($user['email']) . ' | '; // Вывод email пользователя с экранированием HTML-сущностей
        echo '<strong>Age:</strong> ' . $user['age'] . ' years'; // Вывод возраста пользователя

        if (!empty($user['photoPath'])) {
            echo ' | <img src="' . $user['photoPath'] . '" alt="User Photo" style="max-width: 100px;">'; // Если есть фотография пользователя, выводим её
        }

        echo ' | <a href="edit.php?id=' . $user['id'] . '">Edit</a>'; // Ссылка на страницу редактирования пользователя
        echo ' | <a href="delete.php?id=' . $user['id'] . '">Delete</a>'; // Ссылка на страницу удаления пользователя
        echo '</li>'; // Завершение элемента списка
    }
    echo '</ul>'; // Завершение списка
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
</head>
<body>
    <?php renderUsers($users, $uploadDir); ?> <!-- Вызов функции для вывода списка пользователей -->
    <hr> <!-- Горизонтальная линия -->
    <a href="create.php">Add New User</a> <!-- Ссылка для добавления нового пользователя -->
</body>
</html>

