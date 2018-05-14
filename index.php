<!DOCTYPE html>
<?php
    require_once 'auth_functions.php';
    require_once 'db_functions.php';
    
    if (!isAuthorized()) {
        redirect('login.php');
    } else {
        $authorized_user = get_authorized_user();
    }

    $tasks = [];//список задач пользователя
    $users = [];//список пользователей
    $assigned_tasks = [];//список порученных задач
    $params = [];//параметры запроса для передачи
    $id = 0;
    $users = get_users();
    if (isset($_GET)) {
        echo 'GET-1=';var_dump($_GET);echo '<br>';
        $params = filter_input_array(INPUT_GET, $_GET);
        $params['author'] = $authorized_user;
        unset($_GET);
        echo 'GET-2=';var_dump($_GET);echo '<br>';
    }
    if ((isset($_POST)) && (!isset($_POST['password']))) {
        echo 'POST-1=';var_dump($_POST);echo '<br>';
        unset($params);
        $params = filter_input_array(INPUT_POST, $_POST);
        $params['author'] = $authorized_user;
        unset($_POST);
        echo 'POST-2=';var_dump($_POST);echo '<br>';
    }
//    $params['author'] = $authorized_user;
    echo 'params=';var_dump($params);echo '<br>';
    $tasks = do_command($params);
    $assigned_tasks = get_assigned_tasks($authorized_user);
    unset($params);
?>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <title>SQL lesson 3</title>
        <link rel="stylesheet" href="css/styles.css"/>
    </head>
    <body>
        <section class="main-container">
            <h1>Задание к лекции 4.3 «SELECT из нескольких таблиц»</h1>
            <h2>Добро пожаловать, <?=$authorized_user ;?>!</h2>
            <div class="logout">
                <a class="logout-button" href="logout.php">Выход</a>
            </div>
            <div class="part-1">
                <h3>Список задач, созданный вами</h3>
                <div class="form-container">
                    <form method="POST">
                        <input type="text" name="description" placeholder="Описание задачи" value="">
                        <input type="submit" name="save" value="Добавить">
                    </form>
                </div>
                <div class="form-container">
                    <form method="POST">
                        <label for="sort_by">Сортировать по:</label>
                        <select name="sort_by">
                            <option value="date_added">Дате добавления</option>
                            <option value="is_done">Статусу</option>
                            <option value="description">Описанию</option>
                        </select>
                        <input type="submit" name="sort" value="Отсортировать">
                    </form>
                </div>

                <table class="table">
                    <thead class="table-head">
                        <tr class="header-row">
                            <td class="column-description">Описание задачи</td>
                            <td class="column-date">Дата добавления</td>
                            <td class="column-status">Статус</td>
                            <td class="column-action">Действия</td>
                            <td class="column-assigned_user">Ответственный</td>
                            <td class="column-author">Автор</td>
                            <td class="column-assign">Закрепить за</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $record): ?>
                        <tr class="table-row">
                            <?php $id = (int)$record['id']; ?>
                            <td class="column-description"><?=$record['description']; ?></td>
                            <td class="column-date"><?=$record['date_added']; ?></td>
                            <td class="column-status">
                                <?php 
                                    if ($record['is_done'] === 1) {
                                        echo '<span class="task-isdone">выполнено</span>';
                                    } else {
                                        echo '<span class="task-active">в работе</span>';
                                    }
                                ?>
                            </td>
                            <td class="column-action">
                                <a href="?id=<?=$id;?>;action=done">Выполнить</a><br>
                                <a href="?id=<?=$id;?>;action=delete">Удалить</a>
                            </td>
                            <td class="column-assigned_user"><?=$record['assigned_user']; ?></td>
                            <td class="column-author"><?=$authorized_user; ?></td>
                            <td class="column-assign">
                                <form method="POST">
                                    <select name="assigned_user_id">
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?=$user['id'];?>"><?=$user['login']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="task_id" value="<?= $id;?>">
                                    <input type="submit" name="assign" value="Поручить">
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="part-2">
                <h3>Список задач, порученных вам другими пользователями</h3>
                <table class="table">
                    <thead class="table-head">
                        <tr class="header-row">
                            <td class="column-description">Описание задачи</td>
                            <td class="column-date">Дата добавления</td>
                            <td class="column-status">Статус</td>
                            <td class="column-assigned_user">Ответственный</td>
                            <td class="column-author">Автор</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assigned_tasks as $task): ?>
                        <tr class="table-row">
                            <?php $id = (int)$task['id']; ?>
                            <td class="column-description"><?=$task['description']; ?></td>
                            <td class="column-date"><?=$task['date_added']; ?></td>
                            <td class="column-status">
                                <?php 
                                    if ($task['is_done']) {
                                        echo '<span class="task-isdone">выполнено</span>';
                                    } else {
                                        echo '<span class="task-active">в работе</span>';
                                    }
                                ?>
                            </td>
                            <td class="column-assigned_user"><?=$authorized_user; ?></td>
                            <td class="column-author"><?=$task['author']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </body>
</html>
