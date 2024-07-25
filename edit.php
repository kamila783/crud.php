<?php
$errors = []; // Массив для хранения ошибок

// Проверяем, был ли запрос методом GET и задан ли параметр id
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Загрузка данных всех пользователей из файла users.json в массив $users
    $users = json_decode(file_get_contents('users.json'), true);
    $user = null;

    // Поиск пользователя по заданному id
    foreach ($users as $u) {
        if ($u['id'] === $id) {
            $user = $u;
            break;
        }
    }

    // Если пользователь найден
    if ($user) {
        // Если запрос был отправлен методом POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Валидация и получение обновленных данных пользователя
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);

            // Проверка обязательных полей
            if (!$username) {
                $errors[] = "Username is required.";
            }
            if (!$email) {
                $errors[] = "Valid email is required.";
            }
            if ($age === false || $age === null) {
                $errors[] = "Valid age is required.";
            }

            // Проверка и обновление фотографии пользователя
            $photoPath = $user['photoPath'];
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                // Удаление старой фотографии, если она существует
                if (!empty($photoPath) && file_exists($photoPath)) {
                    unlink($photoPath);
                }

                // Загрузка новой фотографии
                $uploadDir = 'uploads/';
                $uploadFile = $uploadDir . uniqid() . '_' . basename($_FILES['photo']['name']);
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                    $photoPath = $uploadFile;
                } else {
                    $errors[] = "Failed to upload photo.";
                }
            } elseif ($_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                $errors[] = "Error uploading photo: " . $_FILES['photo']['error'];
            }

            // Если ошибок нет, обновляем данные пользователя
            if (empty($errors)) {
                // Обновление данных пользователя в массиве $users
                foreach ($users as $key => $u) {
                    if ($u['id'] === $id) {
                        $users[$key]['username'] = $username;
                        $users[$key]['email'] = $email;
                        $users[$key]['age'] = $age;
                        $users[$key]['photoPath'] = $photoPath;
                        break;
                    }
                }

                // Сохранение обновленного списка пользователей в JSON файл
                file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));

                // Перенаправление на главную страницу после успешного обновления
                header('Location: index.php');
                exit;
            }
        }

        // Вывод формы для редактирования данных пользователя
        ?>
        
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit User</title>
        </head>
        <body>
            <h2>Edit User</h2>

            <?php if (!empty($errors)): ?>
                <ul style="color: red;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form action="edit.php?id=<?= $user['id']; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $user['id']; ?>">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
                <br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
                <br>
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?= $user['age']; ?>" required>
                <br>
                <label for="photo">Photo:</label>
                <input type="file" id="photo" name="photo">
                <?php if (!empty($user['photoPath'])): ?>
                    <br>
                    <img src="<?= $user['photoPath']; ?>" alt="User Photo" style="max-width: 100px;">
                <?php endif; ?>
                <br>
                <button type="submit">Update User</button>
            </form>
            <hr>
            <a href="index.php">Back to User List</a>
        </body>
        </html>

        <?php
    } else {
        echo '<p>User not found.</p>'; // Если пользователь не найден
    }
} else {
    echo '<p>Invalid request.</p>'; // Если id не был передан в запросе
}
?>

