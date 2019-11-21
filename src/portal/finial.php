<?php
    include("../includes/connection.php");

    if(!isset($_SESSION['id'])) {
        header("Location: /");
        exit();
    }

    include("../includes/header.php");
    $session_id = $_SESSION['id'];
    $user_id = $_SESSION[$session_id];

    $action = $_GET['a'];
    $aid = $_GET['aid'];

    if($action == "accept") $action = "Accepted";
    if($action == "reject") $action = "Rejected";
    if($action == "comment") {
        $comment = "please add more comments";
        $result = query_with_error("INSERT INTO leave_comments(application_id, comment, user_id) VALUES('$aid','$comment', $user_id)");
    } else {
        $result = query_with_error("UPDATE leave_record SET status = '$action' WHERE application_id = '$aid'");
    }
    header("Location: /portal/leave.php");
?>