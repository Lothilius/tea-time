<?php

include_once 'authentication.php';

function getGoogleMapsJSFilePath() {
    return "https://maps.googleapis.com/maps/api/js?key=AIzaSyDzzYC0JTMf2UPapIJXkNbv9NEobpCBfPQ&sensor=true";
}

function createHeader($cssFiles, $javascriptFiles) {
    if (isset($_COOKIE['firstTime']) && $_COOKIE['firstTime'] == '1') {
        $welcomeMessage = "Welcome Back";
    } else {
        $welcomeMessage = "Welcome";
    }

	echo '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Tea Time</title>
        <link rel="icon" href="favicon.ico" />
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />';

    foreach ($cssFiles as $file) {
        echo '<link rel="stylesheet" type="text/css" href="' . $file . '" media="all" />';
    }

    foreach ($javascriptFiles as $file) {
        echo '<script type ="text/javascript" src = "' . $file . '"></script>';
    }
    echo regularScript();

    echo '
    </head>
    <body>
<!--//    <div class="header">
//        <div class="title"><a href="index.php"><img src="images/treff_medium.png" /></a></div>
//    </div>
-->
    <nav class="nav_bar">
        <ul class = "navList">
            <!-- TODO Include list of compliments -->
            <li><a href="index.php">Hi Beautiful! How are you?</a></li>';

	if(isset($_SESSION['user_Id']) && $_SESSION['user_Id'] != 0){
        echo '<li class="dropBar"><div class="welcomeBar">' . $welcomeMessage . '<img src="images/down_arrow.png" /></div>
            <ul class="dropOut">
                <li><a href="view_entries.php">View Journal Entries</a></li>
                <li>
                    <form action="logout.php" method="POST">
                        <input class="loginButton" type="submit" value="Log Out"  name="logOut"/>
                    </form>
                </li>
            </ul></li>';
	} else {
		echo '<li><form action="login.php" method="POST">
				  <input class="loginButton" type="submit" value="Login" />
				  <input type="hidden" name="redirectUrl" value="'. $_SERVER['PHP_SELF'] . '" />
              </form>';
	}
        
	echo '</ul>
    </nav>';
}

function regularScript()
{
    return '<script>
            require(["dijit/form/Textarea", "dojo/domReady!"], function(Textarea){
            var textarea = new Textarea({
                name: "Description",
                style: "width:300px; height:100px;",
                placeholder: "...was it a good day?"
                }, "myarea").startup();});
	        </script>';
}


//Sanatize request
function clean($value)
{
    return htmlspecialchars($value);
}

//Send to error page
function errorPage()
{
    header('Location: /error.php');
}

//Get the name of the Treff
function getMeetingName($id)
{
    $connect = connectMySql();
    $result = $connect->query("SELECT name FROM Meetings where meetingId='$id'");
    $name = $result->fetch_assoc();
    $name = $name['name'];
    $result->free();

    return $name;
}

//Return emotional rating
function get_rating($rating)
{
    if($rating == 0)
    {
        $emotion = ':)';
    }
    elseif($rating == 1)
    {
        $emotion = ':|';
    }
    elseif($rating == 2)
    {
        $emotion = ':(';
    }

    return $emotion;
}

//Gets the password of user
function getPassword($column, $value)
{
    $connect = connectMySql();

    $result = $connect->query("SELECT password FROM Users where $column='$value'");
    $password = $result->fetch_assoc();
    $password = $password['password'];
    $result->free();
	
	return $password;
}

//Returns if user is anonymous based on session Id. Returns true or false
function isCurrentUserAnonymous($connection) {
	if(!isset($_SESSION['user_Id']) || $_SESSION['user_Id'] == 0 ) {
        return true;
    }

    $user_Id = $_SESSION['user_Id'];
    $result = $connection->query("SELECT anonymous FROM Users where user_Id='$user_Id'");
    $anonymous = $result->fetch_assoc();
    $anonymous = $anonymous['anonymous'];
    $result->free();

    return $anonymous == "1";
}

//Returns if user is anonymous based on email. Returns 1 or 0
function isUserAnonymous($connection, $email) {
    $result = $connection->query("SELECT anonymous FROM Users where email='$email'");
    $anonymous = $result->fetch_assoc();
    $anonymous = $anonymous['anonymous'];
    $result->free();

    return $anonymous == "1";
}

function curl_get($baseUrl, array $data = array(), array $options = array()) {
    // Build the url
    $url = $baseUrl;

    if (count($data) > 0) {
        $url .= "?" . http_build_query($data);
    }

    $defaults = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_SSL_VERIFYPEER => FALSE
    );

    $ch = curl_init();

    curl_setopt_array($ch, ($options + $defaults));
    if(!$result = curl_exec($ch)) {
        trigger_error(curl_error($ch));
    }

    curl_close($ch);
    return $result;
}

class LatLng {
    public $lat;
    public $lng;

    public function __construct($lat, $lng) {
        $this->lat = $lat;
        $this->lng = $lng;
    }
}

function decodePolyLine($string) {
    $points = array();
    $index = $i = 0;
    $previous = array(0,0);
    while( $i < strlen($string)  ) {
        $shift = $result = 0x00;
        do {
            $bit = ord(substr($string,$i++)) - 63;
            $result |= ($bit & 0x1f) << $shift;
            $shift += 5;
        } while( $bit >= 0x20 ) ;

        $diff = ($result & 1) ? ~($result >> 1) : ($result >> 1);
        $number = $previous[$index % 2] + $diff;
        $previous[$index % 2] = $number;
        $index++;
        $points[] = $number * 1e-5;
    }

    $returnPoints = array();
    for ($i = 0; $i < count($points); $i += 2) {
        $returnPoints[$i / 2] = new LatLng($points[$i], $points[$i + 1]);
    }

    return $returnPoints;
}