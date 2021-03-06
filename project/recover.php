<?php
session_start();

include_once 'functions.php';
if (isset($_GET['idHash'])) {
	$hash = $_GET['idHash'];
	startReset($hash);
}
elseif (isset($_POST['forgot'])) {

	confirm();
}

else{
	errorPage();
}

function startReset($hash){
	createHeader(array("style.css"), array("validate.js"));
	echo '
		<div class="main_body">
		<div class=resetTable">
		<h2>Please enter and re-enter your new password to keep Treffing.</h2>
			<form id = "registrationForm" action= "' . $_SERVER['PHP_SELF'] . '" method="POST" onsubmit="return validateNewPass();" >
				<table>
					<tr>
						<td>
							<input class = "valPass" type="password" name="pass1" maxlength="32" placeholder="New Password"/> 
							<span class="valPass"></span>
						</td>
					</tr>
					<tr>
						<td><input type="password" name="pass2" maxlength="32" placeholder="Re-enter Password"/></td>
					</tr>
					<tr>
						<td><div class="requirements">*6-32 characters with at least one number(!, _, - Allowed)</div></td>
					</tr>
				</table>
		</div>
			<div class="resetButton">
				<input class="button" type="submit" value="Reset" name="forgot" />
				<input type="hidden" name="hash" value="'. $hash .'"/>
			</div>
		</form>
	
	</div> <!-- End of main_body -->';
	include 'footer.php';
}


function confirm(){
	$hash= $_POST['hash'];
	$connection = connectMySql();
	$result = $connection->query("Select * 
								  FROM ForgottenPassword
								  WHERE hash='$hash'");
	$row = $result->fetch_assoc();
	if($row['timeOut'] >= date("Y-m-d H:i:s")) {
		$pass1 = crypt($_POST["pass1"]);
		unset($_POST["pass1"]);
		$email = $row['email'];
		$connection->query("UPDATE Users
							SET password = '$pass1'
							WHERE email = '$email'");
		$connection->query("DELETE FROM ForgottenPassword
							WHERE hash='$hash'");
        $result = $connection->query("SELECT user_Id
                                      FROM Users
                                      WHERE email='$email'");

        $row = $result->fetch_assoc();
        $_SESSION['user_Id'] = $row['user_Id'];

        createHeader(array("style.css"), array());
		echo '<h2>Your password has been reset. Happy Treffing!</h2>';
	}
	
	else {
		$connection = connectMySql();
		$connection->query("DELETE FROM ForgottenPassword
							WHERE hash='$hash'");
		
		Echo '<h2> Sorry, your time ran out to change your password.</h2>';
	}
	include 'footer.php';
}