<?php
    include("../includes/connection.php");

    if(!isset($_SESSION['id'])) {
        header("Location: /");
        exit();
    }

    include("../includes/header.php");

    $application_id = $_GET['aid'];
    $comment = $_GET['forward_comment'];
    
    $result = query_with_error("SELECT path, at FROM leave_record WHERE application_id = '$application_id'");
    $row = $result->fetch_assoc();

    $path = explode(",", $row['path']);
    $at = $row['at'];

    $ind = 0;
    foreach($path as $user) {
        if($user == $at) break;
        $ind++;
    }
    $ind++;

    $result = query_with_error("INSERT INTO leave_comments (application_id, comment, user_id) VALUES('$application_id', '$comment', $at);");
    $at = $path[$ind];

    $result = query_with_error("UPDATE leave_record SET at = $at WHERE application_id = '$application_id'");

    header("Location: /portal/leave.php");


?>