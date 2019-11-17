<?php
    include("../includes/connection.php");
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT user_id, username, password FROM employee WHERE email = '$email'";
    $result = $conn->query($sql);

    if($result->num_rows == 1) {
        if($row = $result->fetch_assoc()) {
            if($password == $row['password']) {
                session_start();
                $session_id = session_id();
                $_SESSION['id'] = $session_id;
                $_SESSION[$session_id] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                echo "works fine";
                header("Location: /profile?user=" . $row['username']);
            } else {
                echo "password does not match";
            }
        } else {
            echo mysqli_error($conn);
        }
    } else {
        echo $result->num_rows;
        echo mysqli_error($conn);
    }
?>