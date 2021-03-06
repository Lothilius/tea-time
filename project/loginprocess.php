<?php

session_start();

include_once 'functions.php';

if (isset($_POST["loggedIn"])) {
    if (!checkLogin()) {
        echo '<script type="text/javascript">alert("Incorrect Username or Password"); </script>';
        include 'login.php';
        exit;
    }
}

// Redirect
header("Location: " . $_POST["redirectUrl"]);

function checkLogin() {
    $email = $_POST["name"];
    $pass = $_POST["pass"];
    unset($_POST["pass"]);

    $connection = connectMySql();

    $result = $connection->query("SELECT user_Id, password FROM Users WHERE email = '$email'");

    $row = $result->fetch_assoc();
    $result->free();

    $success = (crypt($pass, $row['password']) == $row['password']);

    if ($success) {
        $_SESSION["user_Id"] = $row['user_Id'];
        // Create the cookie and set it to expire in a week
        setcookie("firstTime", "1", time() + (3600 * 24 * 7), "/");
    }

    $connection->close();

    return $success;
}

function addUser() {
    $email= $_POST["name"];
    $street = $_POST["street"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $zip = $_POST["zip"];
    $pass1 = crypt($_POST["pass1"]);
    unset($_POST["pass1"]);

    $connection = connectMySql();

    $result = $connection->query("SELECT user_Id, anonymous
                                  FROM Users
                                  WHERE email='$email'");
    $row = $result->fetch_assoc();

    // Add user to table
	if ($result->num_rows > 0 && $row['anonymous'] == '1') {
			$connection->query("UPDATE Users
								SET street = '$street', city = '$city', state = '$state', zip = '$zip', password = '$pass1', anonymous = 0
								WHERE email = '$email'");
			$_SESSION["user_Id"] = $row['user_Id'];
			$result->free();
	}
	 else{
		$connection->query("INSERT INTO Users (email, password, street, city, state, zip)
						    VALUES ('$email', '$pass1', '$street', '$city', '$state', '$zip')");

		$_SESSION["user_Id"] = $connection->insert_id;
	}

    // Create the cookie and set it to expire in a week
    setcookie("firstTime", "1", time() + (3600 * 24 * 7), "/");

    $connection->close();
}

function addAnonymousUser() {
    $connection = connectMySql();

    $connection->query("INSERT INTO Users (anonymous) VALUES(TRUE);");

    $_SESSION["user_Id"] = $connection->insert_id;
    // Create the cookie and set it to expire in a week
    setcookie("firstTime", "0", time() + (3600 * 24 * 7), "/");

    $connection->close();
}