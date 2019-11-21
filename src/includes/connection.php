<?php

    $server = "dbapp_mysql";
    $user = "root";
    $pass = "root";
    $db = "employee_management";

    $conn = new mysqli($server, $user, $pass, $db);
    $mng = new MongoDB\Driver\Manager("mongodb://dbapp_mongo");

    
    if($conn->connect_errno) {
        die("Connection to the database failed<br><b>Error No.</b> " . $conn->connect_errno . "<br><b>Error</b> " . $conn->connect_error);
    }
    session_start();

    $mongo_id = "";
    if(isset($_SESSION['id'])) {
        $session_id = $_SESSION['id'];
        $user_id = $_SESSION[$session_id];
        $filter = ["id" => (int)$user_id];
        $query = new  MongoDB\Driver\Query($filter);
        $res = $mng->executeQuery("employee.profile", $query);

        $arr = $res->toArray();
        if(sizeof($arr) != 0) {
            $profile = current($arr);
            $mongo_id = $profile->_id;
        }
    }


    function clear_result(){
        global $conn;

        do {
            if ($res = $conn->store_result()) {
                $res->free();
            }
        } while ($conn->more_results() && $conn->next_result());        

    }

    function query_with_error($sql) {
        clear_result();

        global $conn;
        $result = $conn->query($sql);
        $error = mysqli_error($conn);
        if($error) {
            die("somethin went wrong<br>sql = $sql<br>error = $error");
        }
        return $result;
    }

    function check_error() {
        global $conn;
        $error = mysqli_error($conn);
        if($error) {
            die("somethin went wrong<br>error = $error");
        }
    }

?>
