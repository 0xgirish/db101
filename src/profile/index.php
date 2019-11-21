<?php
    
    include("../includes/connection.php");

    include("../includes/header.php");

    $username = $_GET['user'];
    $sql = "SELECT employee.user_id as user_id, department_id, name, email, address, research_field, designation, address FROM employee, faculty WHERE employee.user_id = faculty.user_id AND employee.username = '" . $username . "'";
    $result = $conn->query($sql);
    $department_id = 0;
    if($result->num_rows == 0 ) {
        $result = query_with_error("SELECT employee.user_id as user_id, name, email, address, designation FROM employee, ccf WHERE employee.user_id = ccf.user_id AND employee.username = '$username'");
        $department_id = -1;
    }

    if($result->num_rows == 0 ) {
        header("Location: /");
        exit();
    }

    $row = $result->fetch_assoc();
    $designation = $row['designation'];

    if($department_id != -1) {
        $sql = "SELECT designation FROM ccf WHERE user_id = " . $row['user_id'];
        $result = $conn->query($sql);
        if($result->num_rows > 0 ) {
            $designation = $designation . ", " . $result->fetch_assoc()['designation'];
        }
    }

    $department_name = "";
    if($department_id == 0) {
        $department_id = $row['department_id'];
        $sql = "SELECT name FROM department WHERE id = " . $department_id;
        $result = $conn->query($sql);
        $department_name = "";
        if($result->num_rows > 0 ) {
            $department_name = $result->fetch_assoc()['name'];
        }
    }
    
    $filter = ["id" => (int)$row['user_id']];
    $query = new  MongoDB\Driver\Query($filter);
    $res = $mng->executeQuery("employee.profile", $query);

    $profile = current($res->toArray());

?>

<div style="margin: 80px 40px 80px 40px">
    <div class="row">
        <div class="col-sm-3"><img src="/img/person.png" style="height:180; width: 150"></div>
        <div class="col-sm-9">
            <h3><?php echo $row['name']; ?></h3>
            <b>Designation</b>: <?php echo $designation; ?><br>
            <b>Email</b>: <?php echo $row['email']; ?><br>
            <?php if($department_id != -1) { ?>
            <b>Department</b>: <?php echo $department_name; ?><br>
            <?php } ?>
            <b>Office Address</b>: <?php echo $row['address']; ?><br>
            <?php if($department_id != -1) { ?>
            <b>Reseach Interests</b>: <?php echo $row['research_field']; ?>
            <?php } ?>
        </div>
    </div>
    <hr>
    <?php foreach ($profile as $title => $content) {
        if($title == '_id' || $title == 'id') continue;    
    ?>
    <div class="row" style="margin-top: 40px">
        <div class="col-sm-12">
        <h4><?php echo $title; $shtitle = sha1($title);
            if(isset($_SESSION['id']) && $_SESSION[$_SESSION['id']] == $row['user_id']) {
                echo " <button class='btn btn-light' id='myBtn$shtitle'><i class='fa fa-edit' style='color: blue'></i> Edit</button>";
            }
        ?></h4>
        <?php echo $content; ?>
        </div>
    </div>
    <hr>
    <?php } ?>
    <?php if(isset($_SESSION['id']) && $_SESSION[$_SESSION['id']] == $row['user_id']) {
        echo " <button class='btn btn-primary' id='myBtn_addField' style='margin-bottom: 40px'><i class='fa fa-plus'></i> Add Field</button>";
    }
    ?>
</div>


<?php if(isset($_SESSION['id']) && $_SESSION[$_SESSION['id']] == $row['user_id']) {
foreach ($profile as $title => $content) {
    if($title == '_id' || $title == 'id') continue;
    $shtitle = sha1($title);
?>

<!-- The Modal -->
<div id="myModal<?php echo $shtitle; ?>" class="modal">
    <!-- Modal content -->
    <div class="modal-content" style="padding-bottom: 40px; width: 110%">
        <div style="margin-down: 20px"><h3>Modify Field</h3></div>
        <form method="get" action="/mongo/modify.php">
        <div class="form-row">
            <input type="text" name="title" class="form-control" placeholder="Header" value="<?php echo $title; ?>">
        </div>
        <div class="form-group">
            <label for="inputAddress">Content</label>
            <textarea class="form-control" rows="5" name="content" placeholder="you can add html too."><?php echo $content; ?></textarea>
        </div>
        <input type="text" name="oldTitle" value="<?php echo $title; ?>" hidden>
        <button type="submit" class="btn btn-primary" style="position:absolute; right: 20px">Submit</button>
        <a href='/mongo/modify.php?delete=<?php echo $title; ?>'><button type='button' class="btn btn-danger" style="position:absolute; left: 20px">Delete</button></a>
        </form>
        <span class="close<?php echo $shtitle; ?>" style="position:absolute; right: 40px;color: #aaaaaa; float: right; font-size: 28px; font-weight: bold;cursor: pointer">&times;</span>
    </div>

</div>

<script>
    var modal<?php echo $shtitle; ?> = document.getElementById("myModal<?php echo $shtitle; ?>");
    var btn<?php echo $shtitle; ?> = document.getElementById("myBtn<?php echo $shtitle; ?>");
    var span<?php echo $shtitle; ?> = document.getElementsByClassName("close<?php echo $shtitle; ?>")[0];

    btn<?php echo $shtitle; ?>.onclick = function() {
        modal<?php echo $shtitle; ?>.style.display = "block";
    }

    span<?php echo $shtitle; ?>.onclick = function() {
        modal<?php echo $shtitle; ?>.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal<?php echo $shtitle; ?>) {
            modal<?php echo $shtitle; ?>.style.display = "none";
        }
    }
</script>

<?php }} ?>

<div id="myModal_addField" class="modal">
    <!-- Modal content -->
    <div class="modal-content" style="padding-bottom: 40px; width: 110%">
        <div style="margin-down: 20px"><h3>Add Field</h3></div>
        <form method="get" action="/mongo/add.php">
        <div class="form-row">
            <input type="text" name="title" class="form-control" placeholder="Header">
        </div>
        <div class="form-group">
            <label for="inputAddress">Content</label>
            <textarea class="form-control" rows="5" name="content" placeholder="you can add html too."></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="position:absolute; right: 20px">Submit</button>
        </form>
        <span class="close_addField" style="position:absolute; right: 40px;color: #aaaaaa; float: right; font-size: 28px; font-weight: bold;cursor: pointer">&times;</span>
    </div>

</div>

<script>
    var modal_addField = document.getElementById("myModal_addField");
    var btn_addField = document.getElementById("myBtn_addField");
    var span_addField = document.getElementsByClassName("close_addField")[0];

    btn_addField.onclick = function() {
        modal_addField.style.display = "block";
    }

    span_addField.onclick = function() {
        modal_addField.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal_addField) {
            modal_addField.style.display = "none";
        }
    }
</script>

<?php include("../includes/footer.php"); ?>