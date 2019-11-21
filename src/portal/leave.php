<?php 
    include("../includes/connection.php");

    if(!isset($_SESSION['id'])) {
        header("Location: /");
        exit();
    }

    include("../includes/header.php");
    $session_id = $_SESSION['id'];
    $user_id = $_SESSION[$session_id];

    $sql = "SELECT leaves, maxleaves FROM available_leaves WHERE user_id = $user_id";
    $result = query_with_error($sql);

    $row = $result->fetch_assoc();
    $leaves = $row['leaves'];
    $maxleaves = $row['maxleaves'];

    $sql = "SELECT employee.user_id as user_id, name, username FROM employee, faculty WHERE employee.user_id = faculty.user_id";
    $result = query_with_error($sql);

    $user_id_map = Array();
    while($row = $result->fetch_assoc()) {
        $user_id_map[$row['user_id']] = [$row['username'], $row['name']];
    }

    $sql = "SELECT employee.user_id, name, username FROM employee, ccf WHERE employee.user_id = ccf.user_id";
    $result = query_with_error($sql);

    $cross_map = Array();
    while($row = $result->fetch_assoc()) {
        $cross_map[$row['user_id']] = [$row['username'], $row['name']];
    }

    $sql = "SELECT application_id, start_date, end_date, borrow, path, at, status FROM leave_record WHERE user_id = $user_id";
    $result = query_with_error($sql);

    $application_id = "none";
    $application_data = Array();
    $action_taken = Array();
    $notifications = Array();
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row['status'] == 'In Process') { 
                $application_id = $row['application_id'];
                $application_data = $row;
            }
            else array_push($action_taken, $row);
        }
    }

    $sql = "SELECT user_id, comment, time FROM leave_comments WHERE application_id = '$application_id' ORDER BY time ASC";
    $result = query_with_error($sql);
    while($row = $result->fetch_assoc()) {
        $comment = $row['comment'];
        $notify = "";
        if($row['user_id'] == $user_id) {
            $datetime = new \DateTime($row['time']);
            $date = $datetime->format('d/m/Y');
            $notify = "You applied for leave on $date, application id is $application_id<br>";
        } else {
            $profile = "";
            $id = $row['user_id'];
            if(isset($user_id_map[$row['user_id']])) {
                $username = $user_id_map[$id][0];
                $name = $user_id_map[$id][1];
                $profile = "<a href='/profile?user=$username'>$name</a>";
            } else {
                $profile = $cross_map[$id][1];
            }
            $notify = $profile . " commented on your application $application_id<br>";
        }
        $notify .= "comment: $comment";
        array_push($notifications, $notify);
    }

    $result = query_with_error("SELECT * FROM leave_record WHERE at = $user_id AND status = 'In Process'");
    $pending_requests = Array();
    while($row = $result->fetch_assoc()) {
        $aid = $row['application_id'];
        $path = array_map('intval', explode(",", $row['path']));
        $approve = ($path[sizeof($path)-1] == $user_id) ? true: false;

        $comment = "";
        $result = query_with_error("SELECT comment, user_id FROM leave_comments WHERE application_id = '$aid'");
        while($r = $result->fetch_assoc()) {
            if(isset($user_id_map[$r['user_id']])) {
                $comment .= $user_id_map[$r['user_id']][1];
            } else {
                $comment .= $cross_map[$r['user_id']][1];
            }
            $comment .= ": " . $r['comment'] . "\n";
        }

        array_push($pending_requests, [$row, $approve, $comment]);
    }

?>


<div style="margin-top: 80px 40px 80px 40px">
    <div class="row" style="padding-top: 60px; height: 425px">
        <div class="col-md-6" style="padding-left: 40px; padding-right: 20px">
            <h4>Employee Information</h4><hr>
            <div class="row">
                <div class="col-sm-3"><img src="/img/person.png" style="height:180; width: 150"></div>
                <div class="col-sm-9" style="padding-left: 30px; padding-top: 20px">
                    <b>username</b>: <?php echo $_SESSION['username']; ?><br>
                    <b>Available Leaves</b>: <?php echo $leaves; ?><br>
                    <b>Leaves/year</b>: <?php echo $maxleaves; ?><br>
                    <?php if($application_id != "none") { ?>
                    <b>Active leave application</b>: <a id="reqBtn" style="color: #1e90ff; text-decoration: underline; cursor: pointer"><?php echo $application_id; ?></a><br>
                    <button class="btn btn-primary" style="margin-top: 20px" id="myBtn" <?php echo "disabled"; ?>>Apply for leave</button>
                    <?php } else {?>
                    <b>Active leave application</b>: <br>
                    <button class="btn btn-primary" style="margin-top: 20px" id="myBtn">Apply for leave</button>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-md-6" style="padding-left: 20px; padding-right: 40px;">
            <h4 style="color: red">Notifications</h4><hr>
            <div style="height: 300px; overflow-y:scroll">
                <?php if(sizeof($notifications) == 0) echo "You don't have any active leave application"; 
                for($i = sizeof($notifications)-1; $i >= 0; $i--) { ?>
                    <div class="alert alert-primary" role="alert"><?php echo $notifications[$i]; ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php if(sizeof($pending_requests) != 0) { ?>
    <div class="row" style="padding-left: 40px; padding-right: 40px; margin-top: 60px">
        <div class="col-sm-12">
            <h5>Approval Required by you</h5>
            <table class="table">
            <thead>
                <tr>
                <th scope="col">Application ID</th>
                <th scope="col">Applicant</th>
                <th scope="col">Start date</th>
                <th scope="col">End date</th>
                <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $x = 1;
                foreach($pending_requests as $rows) {
                    $row = $rows[0]; $approve = $rows[1];
                    ?>
                <tr>
                <td><?php echo $row['application_id']; ?></td>
                <td><?php if(isset($user_id_map[$row['user_id']])) {
                    $username = $user_id_map[$row['user_id']][0];
                    $name = $user_id_map[$row['user_id']][1];
                    echo "<a href='/profile?user=$username'>$name</a>";
                } else {
                    echo $cross_map[$row['user_id']][1];
                } ?></td>
                <td><?php echo $row['start_date']; ?></td>
                <td><?php echo $row['end_date']; ?></td>
                <td>
                <button class="btn btn-primary" id="myBtn<?php echo $x; ?>">View</button>
                </td>
                </tr>
                <?php $x++; } ?>
            </tbody>
            </table>
        </div>
    </div>
    <?php } ?>
    <div class="row" style="padding-left: 40px; padding-right: 40px; margin-top: 60px">
        <div class="col-sm-12">
            <h5>Your leave history</h5>
            <table class="table">
            <thead>
                <tr>
                <th scope="col">Application ID</th>
                <th scope="col">Start date</th>
                <th scope="col">End date</th>
                <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($action_taken as $row) { ?>
                <tr>
                <td><?php echo $row['application_id']; ?></td>
                <td><?php echo $row['start_date']; ?></td>
                <td><?php echo $row['end_date']; ?></td>
                <td><?php echo $row['status']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
    </div>
</div>


<?php $x = 1;
    foreach($pending_requests as $rows) {
        $row = $rows[0];
        $approve = $rows[1];
        $comment = $rows[2];
?>
<!-- The Modal -->
<div id="myModal<?php echo $x; ?>" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div style="margin-down: 20px"><h3>Leave Application</h3></div>
        <form method="get" action="/portal/forward.php">
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="leaveFrom">Start date</label>
            <input type="date" class="form-control" name="startdate" id="startdate" value="<?php echo $row['start_date']; ?>" disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="leaveTO">End date</label>
            <input type="date" class="form-control" id="enddate" name="enddate" value="<?php echo $row['end_date']; ?>" disabled>
            </div>
        </div>
        <div class="form-group">
            <label for="inputAddress">Applicant's Comment</label>
            <textarea class="form-control" id="leaveReason" rows="3" name="comment" disabled><?php echo $comment; ?></textarea>
        </div>
        <div class="form-group">
            <label for="inputAddress">Your Comment</label>
            <textarea class="form-control" id="leaveReason" rows="3" name="forward_comment" placeholder="add comment for further review"></textarea>
        </div>
        <div class="form-row">
        <div class="form-check" style="margin-bottom: 20px">
            <input type="checkbox" name="borrow" class="form-check-input" id="borrowLeave" <?php echo ($row['borrow'] == 1) ? 'checked' : ''; ?> disabled>
            <label class="form-check-label" for="borrowLeave">Borrow Leave</label>
        </div>
        <input type="text" name="aid" value="<?php echo $row['application_id']; ?>" hidden>
        </div>
        <?php if(!$approve) { ?>
        <button type="submit" class="btn btn-primary" style="position:absolute; right: 20px; bottom: 6px">Forward</button>
        <a href='/portal/finial.php?a=comment&aid=<?php echo $row['application_id']; ?>'><button type="button" class="btn btn-info" style="position:absolute; left: 20px; bottom: 6px">Ask For Comments</button></a>
        <?php } else { ?>
        <a href="/portal/finial.php?a=accept&aid=<?php echo $row['application_id']; ?>"><button type="button" class="btn btn-success" style="position:absolute; right: 20px; bottom: 6px">Accept</button></a>
        <a href="/portal/finial.php?a=comment&aid=<?php echo $row['application_id']; ?>"><button type="button" class="btn btn-info" style="position:absolute; left: 175px; bottom: 6px">Comment</button></a>
        <a href="/portal/finial.php?a=reject&aid=<?php echo $row['application_id']; ?>"><button type="button" class="btn btn-danger" style="position:absolute; left: 20px; bottom: 6px">Reject</button></a>
        <?php } ?>
        </form>
        <span class="close<?php echo $x; ?>" style="position:absolute; right: 40px;color: #aaaaaa; float: right; font-size: 28px; font-weight: bold;cursor: pointer">&times;</span>
    </div>

<script>
    var modal<?php echo $x; ?> = document.getElementById("myModal<?php echo $x; ?>");
    var btn<?php echo $x; ?> = document.getElementById("myBtn<?php echo $x; ?>");
    var span<?php echo $x; ?> = document.getElementsByClassName("close<?php echo $x; ?>")[0];

    btn<?php echo $x; ?>.onclick = function() {
        modal<?php echo $x; ?>.style.display = "block";
    }

    span<?php echo $x; ?>.onclick = function() {
        modal<?php echo $x; ?>.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal<?php echo $x; ?>) {
            modal<?php echo $x; ?>.style.display = "none";
        }
    }

</script>

</div>
<?php $x++; } ?>

<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div style="margin-down: 20px"><h3>Leave Application</h3></div>
        <form method="get" action="/portal/apply.php">
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="leaveFrom">Start date</label>
            <input type="date" class="form-control" name="startdate" id="startdate" placeholder="Leave start date">
            </div>
            <div class="form-group col-md-6">
            <label for="leaveTO">End date</label>
            <input type="date" class="form-control" id="enddate" name="enddate" placeholder="Leave finish date">
            </div>
        </div>
        <div class="form-group">
            <label for="inputAddress">Comment</label>
            <textarea class="form-control" id="leaveReason" rows="3" name="comment" placeholder="reason for leave"></textarea>
        </div>
        <div class="form-row">
        <div class="form-check">
            <input type="checkbox" name="borrow" class="form-check-input" id="borrowLeave">
            <label class="form-check-label" for="borrowLeave">Borrow Leave</label>
        </div>
        <button type="submit" class="btn btn-primary" style="position:absolute; right: 20px">Apply</button>
        </div>
        </form>
        <span class="close" style="position:absolute; right: 40px;color: #aaaaaa; float: right; font-size: 28px; font-weight: bold;cursor: pointer">&times;</span>
    </div>

</div>

<?php if($application_id != "none") {
    $result = query_with_error("SELECT comment FROM leave_comments WHERE application_id = '$application_id' AND user_id = $user_id");
    $comment = $result->fetch_assoc()['comment'];

    $result = query_with_error("SELECT * FROM leave_record WHERE application_id = '$application_id'");
    $row = $result->fetch_assoc();

?>
<!-- The Modal -->
<div id="reqModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div style="margin-down: 20px"><h3>Leave Application</h3></div>
        <form method="get" action="/portal/comment.php">
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="leaveFrom">Start date</label>
            <input type="date" class="form-control" name="startdate" id="startdate" placeholder="Leave start date" value="<?php echo $row['start_date']; ?>" disabled>
            </div>
            <div class="form-group col-md-6">
            <label for="leaveTO">End date</label>
            <input type="date" class="form-control" id="enddate" name="enddate" placeholder="Leave finish date" value="<?php echo $row['end_date']; ?>" disabled>
            </div>
        </div>
        <div class="form-group">
            <label for="inputAddress">Comment</label>
            <textarea class="form-control" id="leaveReason" rows="3" name="comment" placeholder="reason for leave"><?php echo $comment; ?></textarea>
        </div>
        <div class="form-row">
        <div class="form-check">
            <input type="checkbox" name="borrow" class="form-check-input" id="borrowLeave" value="<?php echo ($row['borrow'] == 1) ? 'ON' : 'OFF' ?>" disabled>
            <label class="form-check-label" for="borrowLeave">Borrow Leave</label>
        </div>
        <input type="text" name="aid" value="<?php echo $application_data['application_id']; ?>" hidden>
        <button type="submit" class="btn btn-info" style="position:absolute; right: 20px">Comment</button>
        </div>
        </form>
        <span class="reqclose" style="position:absolute; right: 40px;color: #aaaaaa; float: right; font-size: 28px; font-weight: bold;cursor: pointer">&times;</span>
    </div>

</div>
<?php } ?>

<script>

    var reqmodal = document.getElementById("reqModal");
    var reqbtn = document.getElementById("reqBtn");
    var reqspan = document.getElementsByClassName("reqclose")[0];

    reqbtn.onclick = function() {
        reqmodal.style.display = "block";
    }

    reqspan.onclick = function() {
        reqmodal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == reqmodal) {
            reqmodal.style.display = "none";
        }
    }

</script>


<?php include("../includes/footer.php"); ?>