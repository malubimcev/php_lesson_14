<?php

class Database
{
    private $db = NULL;
    private $recordset = [];
    
    public function get_all_users()
    {
        $request = 'SELECT
                        id AS id,
                        login,
                        password
                    FROM
                        user
                    ORDER BY
                        login ASC';
        $params = [
            '' => ''
        ];
        $this -> recordset = $this -> do_request($request, $params);
        if (empty($this -> recordset)) {
            $this -> recordset = $this -> get_empty_users();
        }
        return $this -> recordset;
    }
    
    public function get_user_by_name($login)
    {
        $request = 'SELECT
                        id AS id,
                        login,
                        password
                    FROM
                        user
                    WHERE
                        login = :login';
        $params = [
            ':login' => $login
        ];
        $this -> recordset = $this -> do_request($request, $params);
        if (empty($this -> recordset)) {
            $this -> recordset = $this -> get_empty_users();
        }
        return $this -> recordset[0];
    }
    
    public function get_tasks_by_user($user_name, $sort_param)
    {
        $user_id = $this -> get_user_id($user_name);
        $request = 'SELECT
                        task.id AS id,
                        task.description AS description,
                        task.date_added AS date_added,
                        user.login AS author,
                        task.is_done AS is_done
                    FROM
                        task 
                    INNER JOIN
                        user
                    ON
                        user.id = task.user_id
                    WHERE
                        task.assigned_user_id = :user_id
                        AND task.user_id <> :user_id
                    ORDER BY ';
        $request .= $sort_param;
        $params = [
            ':user_id' => $user_id
        ];
        $this -> recordset = $this -> do_request($request, $params);
        if (!isset($this -> recordset)) {
            $this -> recordset = $this -> get_empty_tasks();
        }
        return $this -> recordset;
    }
    
    public function get_tasks_by_author($author_name, $sort_param)
    {
        $author_id = $this -> get_user_id($author_name);
        $request = 'SELECT
                        task.id AS id,
                        task.description AS description,
                        task.date_added AS date_added,
                        user.login AS assigned_user,
                        task.is_done AS is_done
                    FROM
                        task
                    INNER JOIN
                        user
                    ON
                        user.id = task.assigned_user_id
                    WHERE
                        task.user_id = :author_id 
                    ORDER BY ';
        $request .= $sort_param;
        $params = [
            ':author_id' => $author_id
        ];
        $this -> recordset = $this -> do_request($request, $params);
        if (!isset($this -> recordset)) {
            $this -> recordset = $this -> get_empty_tasks();
        }
        return $this -> recordset;
    }
    
    public function add_user($login, $password)
    {
        $is_exist = $this -> get_user_id($login);
        if ($is_exist === 0) {
            $request = 'INSERT INTO user (
                            login,
                            password)
                        VALUES (
                            :login,
                            :password)';
            $params = [
                ':login' => $login,
                ':password' => $password
            ];
            $this -> do_request($request, $params);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function add_task($description, $author, $user)
    {
        $author_id = $this -> get_user_id($author);
        $assigned_user_id = $this -> get_user_id($user);
        $request = 'INSERT INTO task (
                        description,
                        is_done,
                        user_id,
                        assigned_user_id)
                    VALUES (
                        :description,
                        0,
                        :user_id,
                        :assigned_user_id)';
        $params = [
            ':description' => $description,
            ':user_id' => $author_id,
            ':assigned_user_id' => $assigned_user_id
        ];
        $this -> do_request($request, $params);
        return;
    }
    
    public function close_task($id)
    {
        $request = 'UPDATE
                        task
                    SET
                        is_done=:is_done
                    WHERE 
                        id=:id';
        $params = [
            ':is_done' => 1,
            ':id' => $id
        ];
        $this -> do_request($request, $params);
        return;
    }
    
    public function delete_task($id)
    {
        $request = 'DELETE FROM
                        task
                    WHERE
                        id=:id';
        $params = [
            ':id' => $id
        ];
        $this -> do_request($request, $params);
        return;
    }
    
    public function assign_task($task_id, $user_id)
    {
        $request = 'UPDATE
                        task
                    SET
                        assigned_user_id=:user_id
                    WHERE
                        id=:id';
        $params = [
            ':user_id' => $user_id,
            ':id' => $task_id
        ];
        $this -> do_request($request, $params);
        return;
    }
    

    private function get_connection()//создаем и возвращаем объект PDO
    {
        require_once 'config.php';//подключение файла конфигурации параметров соединения
        try {
            $pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            return $pdo;
        } catch (Exception $error) {
            return NULL;
        }
    }
    
    public function __construct() {
        $this -> db = $this -> get_connection();//тестируем подключение
        if (!isset($this -> db)) {
            die('Не удалось подключиться к базе данных');
        }
    }
    
    private function get_user_id($login)
    {
        return $this -> get_user_by_name($login)['id'];
    }

    private function do_request($request, $params)//выполняет запрос с параметрами
    {
        $results = [];
        $stmt = NULL;
        try {
            $this -> db = $this -> get_connection();
            $stmt = $this -> db -> prepare($request);
            var_dump($params);
            echo '<br>';
            foreach ($params as $key => $value) {
                $stmt -> bindValue($key, $value);
            }
            $stmt -> execute();
            $this -> db = NULL;
            if (isset($stmt)) {
                while ($row = $stmt -> fetch()) {
                    $results[] = $row;
                }
            } else {
                $results = NULL;
            }
        } catch (Exception $error) {
            echo $error -> getMessage();
        }
        return $results;
    }
    
    private function get_empty_tasks()//возвращает пустой набор задач
    {
        $empty_set = [
            'description' => '-',
            'date_added' => '-',
            'is_done' => '-',
            'user_name' => '-',
            'author' => '-'
        ];
        return $empty_set;
    }
    
    private function get_empty_users()//возвращает пустой набор пользователей
    {
        $empty_set = [
            'id' => 0,
            'login' => '-',
            'password' => '-',
        ];
        return $empty_set;
    }
    
}//===end class===