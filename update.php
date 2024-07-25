<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение и фильтрация данных из POST запроса
    $id = $_POST['id'];
    $newUsername = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $newEmail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $newAge = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);

    // Загрузка данных пользователей из JSON файла
    $users = json_decode(file_get_contents('users.json'), true);

    // Поиск пользователя по ID и обновление данных
    foreach ($users as $key => $user) {
        if ($user['id'] === $id) {
            // Обновление данных пользователя в массиве $users
            $users[$key]['username'] = $newUsername;
            $users[$key]['email'] = $newEmail;
            $users[$key]['age'] = $newAge;

            // Обработка загрузки новой фотографии, если она была загружена
            $photoPath = $users[$key]['photoPath'];
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                // Удаление старой фотографии, если она существует
                if (!empty($photoPath) && file_exists($photoPath)) {
                    unlink($photoPath);
                }

                // Загрузка новой фотографии
                $uploadDir = 'uploads/';
                $uploadFile = $uploadDir . uniqid() . '_' . basename($_FILES['photo']['name']);
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                    $users[$key]['photoPath'] = $uploadFile; // Обновление пути к фотографии в массиве данных пользователя
                } else {
                    echo "Failed to upload photo."; // Вывод сообщения об ошибке при загрузке фотографии
                    exit;
                }
            } elseif ($_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                echo "Error uploading photo: " . $_FILES['photo']['error']; // Вывод сообщения об ошибке загрузки фотографии
                exit;
            }

            break; // Завершение цикла после обновления данных пользователя
        }
    }

    // Сохранение обновленного списка пользователей в JSON файл
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));

    // Перенаправление на главную страницу после успешного обновления данных
    header('Location: index.php');
    exit;
} else {
    echo "Invalid request."; // Вывод сообщения об ошибке при недопустимом типе запроса
}



