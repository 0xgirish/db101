<?php
    
    include("../includes/connection.php");
    session_start();

    include("../includes/header.php");

    $username = $_GET['user'];
    $sql = "SELECT employee.user_id as user_id, department_id, name, email, address, research_field, designation, address FROM employee, faculty WHERE employee.user_id = faculty.user_id AND employee.username = '" . $username . "'";
    $result = $conn->query($sql);
    if($result->num_rows == 0 ) {
        die("Page not found: 404");
    }
    $row = $result->fetch_assoc();
    $designation = $row['designation'];

    $sql = "SELECT designation FROM ccf WHERE user_id = " . $row['user_id'];
    $result = $conn->query($sql);
    if($result->num_rows > 0 ) {
        $designation = $designation . ", " . $result->fetch_assoc()['designation'];
    }

    $department_id = $row['department_id'];
    $sql = "SELECT name FROM department WHERE id = " . $department_id;
    $result = $conn->query($sql);
    $department_name = "";
    if($result->num_rows > 0 ) {
        $department_name = $result->fetch_assoc()['name'];
    }

?>

<div style="margin: 80px 40px 80px 40px">
    <div class="row">
        <div class="col-sm-3"><img src="/img/person.png" style="height:180; width: 150"></div>
        <div class="col-sm-9">
            <h3><?php echo $row['name']; ?></h3>
            <b>Designation</b>: <?php echo $designation; ?><br>
            <b>Email</b>: <?php echo $row['email']; ?><br>
            <b>Department</b>: <?php echo $department_name; ?><br>
            <b>Office Address</b>: <?php echo $row['address']; ?><br>
            <b>Reseach Interests</b>: <?php echo $row['research_field']; ?>
        </div>
    </div>
    <hr>
    <div class="row" style="margin-top: 40px">
        <div class="col-sm-12">
        <h4>Biography</h4>
        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.
        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.
        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.
        </div>
    </div>
</div>


<?php include("../includes/footer.php"); ?>