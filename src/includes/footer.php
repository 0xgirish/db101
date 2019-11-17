<?php if(!isset($_SESSION['id'])) { ?>
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <div><h3>Faculty Portal</h3></div>
            <form style="margin-top: 30px" method="post" action="/login/index.php">
            <div class="form-group">
                <input type="email" name="email" class="form-control" id="emailLogin" aria-describedby="emailHelp" placeholder="Institute email address">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" id="passwordLogin" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <span class="close" style="position:absolute; right: 40px;color: #aaaaaa; float: right; font-size: 28px; font-weight: bold;">&times;</span>
        </div>

    </div>
<?php } ?>

</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="/js/bootstrap.min.js"></script>

<script>
    var modal = document.getElementById("myModal");
    var btn = document.getElementById("myBtn");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
