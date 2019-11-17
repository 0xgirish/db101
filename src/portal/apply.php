<?php
    include("../includes/connection.php");
    if(!isset($_SESSION['id'])) {
        header("Location: /");
    }
    $session_id = $_SESSION['id'];
    $user_id = $_SESSION[$session_id];


    $start_date = $_GET['startdate'];
    $end_date = $_GET['enddate'];
    $comment = $_GET['comment'];
    $borrow = (isset($_GET['borrow'])) ? 1 : 0;

    $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);

    $sql = "SELECT leaves, maxleaves FROM available_leaves WHERE user_id = " . $user_id;
    $result = query_with_error($sql);
    if($result->num_rows == 0) {
        die("no entry of leaves for user $user_id");
    }

    $row = $result->fetch_assoc();
    $leaves = $row['leaves'];
    $maxleaves = $row['maxleaves'];


    if($days <= $leaves || $borrow == 1) {
        if($days > $leaves + $maxleaves) {
            // TODO: send error message to leave portal
            die("too many leaves");
        }
        // generate unique application id
        $now = new \DateTime('now');
        $application_id = $now->format('Ymd'). "-" . rand(10, 99) . "-" . $user_id;

        // get the highest designation of the faculty
        $sql = "SELECT designation FROM ccf WHERE user_id = $user_id";
        $result = query_with_error($sql);
        $designation = "";
        if($result->num_rows > 0) {
            $designation = $result->fetch_assoc()['designation'];
        }

        if($designation == "") {
            $sql = "SELECT designation FROM faculty WHERE user_id = $user_id";
            $result = query_with_error($sql);
            if($result->num_rows > 0) {
                $designation = $result->fetch_assoc()['designation'];
            }
        }

        $result = query_with_error("SELECT to_v FROM leave_route where from_u = '$designation'");
        if($result->num_rows == 0) die("error: leave route for $designation doesn't exist");
        $path = explode(",", $result->fetch_assoc()['to_v']);
        $user_path = Array();

        foreach($path as $desig) {
            if($desig == "HOD") {
                $result = query_with_error("SELECT faculty.user_id as user_id FROM faculty, ccf WHERE ccf.designation = '$desig' AND faculty.user_id = ccf.user_id AND faculty.department_id IN (SELECT department_id FROM faculty WHERE faculty.user_id = $user_id)");
                array_push($user_path, $result->fetch_assoc()['user_id']);
            } else {
                $result = query_with_error("SELECT user_id FROM ccf WHERE ccf.designation = '$desig'");
                array_push($user_path, $result->fetch_assoc()['user_id']);
            }
        }
        
        $at = $user_path[0];
        $user_path_string = implode(",", $user_path);
        // add the leave request to leave_record and comment to leave_comment
        $sql = "INSERT INTO leave_record (application_id, user_id, start_date, end_date, borrow, path, at) VALUES('$application_id', $user_id, '$start_date', '$end_date', $borrow, '$user_path_string', $at);";
        $sql .= "INSERT INTO leave_comments (application_id, comment, user_id) VALUES('$application_id', '$comment', $user_id)";
        $result = $conn->multi_query($sql);
        check_error();

        header("Location: /portal/leave.php");
    } else {
        // TODO: send error message to leave portal
        die("too many leaves");
    }

?>