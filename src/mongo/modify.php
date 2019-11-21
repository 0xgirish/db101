<?php 
    include("../includes/connection.php");

    if(!isset($_SESSION['id'])) {
        header("Location: /");
        exit();
    }

    $session_id = $_SESSION['id'];
    $user_id = $_SESSION[$session_id];
    $username = $_SESSION['username'];

    $bulk = new MongoDB\Driver\BulkWrite();
    if(isset($_GET['delete'])) {
        $dtitle = $_GET['delete'];
        $bulk->update(['_id' => $mongo_id], ['$unset' => [$dtitle => "" ]]);
    } else {
        $title = $_GET['title'];
        $content = $_GET['content'];
        $oldTitle = $_GET['oldTitle'];

        $bulk->update(['_id' => $mongo_id], ['$set' => [$oldTitle => $content ]]);
        if($title != $oldTitle) {
            $bulk->update(['_id' => $mongo_id], ['$rename' => [$oldTitle => $title]]);
        } 
    }
    $mng->executeBulkWrite('employee.profile', $bulk);

    header("Location: /profile?user=$username");

?>