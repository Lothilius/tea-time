<?php
session_start();

if (isset($_SESSION['user_Id']) && $_SESSION['user_Id'] != 0) {
    header("Location: create2.php");
}

include_once 'functions.php';
createHeader(array("style.css"), array());

echo '
<div class="main_body">
	<div class="login">
        <form action="login.php" method="POST">
            <input class="button" type="submit" value="Login/Register" />
            <input type="hidden" name="redirectUrl" value="create2.php" />
        </form>
	</div>
</div> <!-- End of main_body -->';

include 'footer.php';
