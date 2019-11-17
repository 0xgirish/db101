<?php
    
    include("../includes/connection.php");
    session_start();

    include("../includes/header.php");
?>

<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
        <img  src="/img/<?php echo $_GET['d']; ?>.jpg" style="height:400px; width:100%" alt="<?php echo $_GET['d']; ?>">
        </div>
    </div>
</div>

<div style="margin-top: 40px; margin-down: 80px">
<!-- TODO: change for loop to database content -->
<?php
$sql = "SELECT name, username, email, phd, research_field FROM faculty, employee where faculty.user_id = employee.user_id and faculty.department_id = " . $_GET['id'];
$result = $conn->query($sql);
if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) { ?>
        <div class="row" style="margin: 30px 10px 30px 10px;  padding: 10px; box-shadow: 2px 3px 8px black">
            <div class="col-sm-2" style="padding-right: 0px"><img src="/img/person.png" style="height:120; width: 100px"></div>
            <div class="col-sm-10" class="profile" style="padding-left: 0px">
                <p style="color: red"><?php echo $row['name']; ?></p>
                <b>Web Address</b> : <a href="/profile?user=<?php echo $row['username']; ?>" style="color: red">Click Here</a><br>
                <b>Email-id</b> : <?php echo $row['email']; ?><br>
                <b>PhD</b> : <?php echo $row['phd']; ?><br> 
                <b>Research Interests</b>: <?php echo $row['research_field']; ?>
            </div>
        </div>
    <?php }
} ?>
</div>

<?php include("../includes/footer.php"); ?>