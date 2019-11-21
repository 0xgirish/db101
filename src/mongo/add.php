<?php 
    include("../includes/connection.php");

    if(!isset($_SESSION['id'])) {
        header("Location: /");
        exit();
    }

    $session_id = $_SESSION['id'];
    $user_id = $_SESSION[$session_id];
    $username = $_SESSION['username'];

    $title = $_GET['title'];
    $content = $_GET['content'];

    $bulk = new MongoDB\Driver\BulkWrite();
    if($mongo_id != "") {
        $bulk->update(['_id' => $mongo_id], ['$set' => [$title => $content ]]);
    } else {
        $bulk->insert(['_id' => new MongoDB\BSON\ObjectID, 'id' => (int)$user_id, $title => $content]);
    }
    $mng->executeBulkWrite('employee.profile', $bulk);

    header("Location: /profile?user=$username");
?>