<?php
    require_once 'db_functions.php';
    session_start();
    $errors = [];
    
    //$pass = password_hash("1234", PASSWORD_DEFAULT);
    
    function login($userName, $password) //функция проверки логина и пароля
    {
        $user = getUser($userName);
        if (!$user) {
            return FALSE;
        } else {
            if ($user['password'] === $password) {
                $_SESSION['user'] = $user;
                return TRUE;
            }
        }
    }

    function getUser($userName) //функция получения пользователя по имени
    {
        $db = get_database();
        $user = $db -> get_user_by_name($userName);
        if (isset($user)) {
            return $user;
        } else {
            return NULL;
        }
    }
    
    function get_authorized_user()
    {
        return $_SESSION['user']['login'];
    }

    function isAuthorized()
    {
        return !empty($_SESSION['user']);
    }

    function saveUser($userName, $userPassword)
    {
        $db = get_database();
        $db -> add_user($userName, $userPassword);
    }

    function redirect($page)
    {
	header("Location: $page");
	die;
    }

    function logout()
    {
	session_destroy();
	redirect('login.php');
    }
