<?php
// Проверка метода запроса (должен быть GET) и наличия параметра id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id']; // Получаем id пользователя для удаления

    // Загрузка данных пользователей из JSON файла в массив $users
    $users = json_decode(file_get_contents('users.json'), true);

    // Поиск пользователя по id и удаление из массива
    foreach ($users as $key => $user) {
        if ($user['id'] === $id) {
            // Удаление файла фотографии, если он существует
            if (!empty($user['photoPath']) && file_exists($user['photoPath'])) {
                unlink($user['photoPath']); // Удаление файла фотографии
            }

            // Удаление пользователя из массива $users
            unset($users[$key]);
            break; // Выход из цикла foreach после удаления пользователя
        }
    }

    // Сохранение обновленного списка пользователей в JSON файл
    file_put_contents('users.json', json_encode(array_values($users), JSON_PRETTY_PRINT));

    // Перенаправление на главную страницу после успешного удаления
    header('Location: index.php');
    exit; // Завершение скрипта после перенаправления
} else {
    echo "Invalid request."; // В случае неверного запроса или отсутствия параметра id
}


