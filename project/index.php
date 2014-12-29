<?php

session_start();
if (isset($_SESSION['user_Id']) && $_SESSION['user_Id'] != 0) {
    header("Location: " . $_POST["redirectUrl"]);
}

include_once 'functions.php';
createHeader(array("style.css"), array("http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js#sthash.J5zZTqH1.dpuf", "validate.js"));

echo '
<div class="main_body">
	<div class="login">
		<div class="create">
			<a href="create1.php">Make a Journal Entry</a>
		</div>
		<div class="join">
			<a href="view_entries.php">Look at Your Past</a>
		</div>
	</div> <!--// End of login -->
</div><!--// End of main_body -->';

include 'footer.php';
