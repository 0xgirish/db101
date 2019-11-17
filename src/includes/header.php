<html>
    <head>
        <title>Faculty Portal IIT RPR</title>
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/style.css">
    </head>
    
    <body>
        <div style="margin-left: 84px; margin-right: 84px; background-color: #fffacc">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand left" href="/">IIT Ropar Faculty Portal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="/">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Departments</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <!-- TODO: make departments dynamic-->
                    <?php 
                        $sql = "SELECT id, code from department";
                        $result = $conn->query($sql);
                        if($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<a class='dropdown-item' href='/department?d=". $row['code'] ."&id=". $row['id'] ."'>". strtoupper($row['code']) ."</a>";
                            }
                        }
                    ?>
                </div>
            </li>
            <?php if(isset($_SESSION['id'])) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="/portal/leave.php">Leave Portal</a>
                </li>
                <li class="nav-item" style="position:absolute; right: 80px">
                    <a class="nav-link" href="/profile?user=<?php echo $_SESSION['username']; ?>"><?php echo $_SESSION['username'] ?></a>    
                </li>
                <li class="nav-item" style="position:absolute; right: 2px">
                    <a class="nav-link" href="/logout">Logout</a>
                </li>
            <?php } else { ?>
                <li class="nav-item" style="position:absolute; right: 2px">
                    <button class="btn btn-dark" id="myBtn">Login</button>    
                </li>
            <?php } ?>
            </ul>
        </div>
        </nav>
