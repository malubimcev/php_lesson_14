<?php
    require_once 'auth_functions.php';
    
    //redirect('index.php');//===для отладки===

    $errorArray = [];//массив для записи ошибок
    $result = '';//для вывода результата на страницу
    if ((isset($_POST['user_name'])) && (isset($_POST['user_password']))) {
        $login = $_POST['user_name'];
        $password = $_POST['user_password'];
        if (isset($_POST['enter'])) {
            if (login($login, $password)) {
                redirect('index.php');
            } else {
                $result = 'Пользователь не зарегистрирован';
            }
        }
        if (isset($_POST['register'])) {
            saveUser($login, $password);
            $result = 'Вы зарегистрированы';
        } 
    } else {
        $result = 'Введите имя и пароль';
    }
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <title>Авторизация</title>
    <link rel="stylesheet" href="css/styles.css"/>
  </head>
  <body>
      <section class="main-container">
        <h1>Задание к лекции 4.3 «SELECT из нескольких таблиц»</h1>
        <h2>Авторизация</h2>
        <div class="result">
            <?php echo $result; ?>
        </div>
        <div class="form-container">
            <form action="<?=$_SERVER['PHP_SELF'];?>" method="POST" class="user-input-form">
                <input type="text" name="user_name" placeholder="Имя пользователя" value=""> 
                <input type="password" name="user_password" placeholder="Пароль" value="">
                <input type="submit" name="enter" value="Вход" class="button select-button">
                <input type="submit" name="register" value="Регистрация" class="button select-button">
            </form><br>
            <a class="logout-button" href="logout.php">Выход</a>
        </div>
    </section>
  </body>
</html>
