
<?php 
    include("../includes/connection.php");

    if(!isset($_SESSION['id'])) {
        header("Location: /");
        exit();
    }

    include("../includes/header.php");
    $session_id = $_SESSION['id'];
    $user_id = $_SESSION[$session_id];

    $comment = $_GET['comment'];
    $aid = $_GET['aid'];

    $result = query_with_error("UPDATE leave_comments SET comment = '$comment' WHERE application_id = '$aid' AND user_id = $user_id");
    header("Location: /portal/leave.php");

?>