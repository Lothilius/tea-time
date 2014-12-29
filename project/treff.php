<?php

session_start();

$entry_Id = $_GET['entry_Id'];
include_once 'functions.php';

createHeader(array("style.css"), array());

$connection = connectMySql();
//var_dump("SELECT meetingId, userId, startingStreet, startingCity, startingState, startingZip, startingCountry FROM MeetingUsers WHERE idHash='$id'");
$result = $connection->query("SELECT meetingId, userId, startingStreet, startingCity, startingState, startingZip, startingCountry FROM MeetingUsers WHERE idHash='$id'");

$row = $result->fetch_assoc();
$meetingId = $row['meetingId'];
$userId = $row['userId'];
$startingAddress = $row['startingStreet'] . ", " . $row['startingCity'] . ", " . $row['startingState'] . " " . $row['startingZip'] . ", " . $row['startingCountry'];
$rating = get_rating($row['rating']);
$result->free();

$result = $connection->query("SELECT midpointStreet, midpointCity, midpointState, midpointZip, midpointCountry, midpointName, status
                              FROM Meetings
                              WHERE meetingId=$meetingId");

$row = $result->fetch_assoc();
$midpointAddress = $row['midpointStreet'] . ", " . $row['midpointCity'] . ", " . $row['midpointState'] . " " . $row['midpointZip'] . ", " . $row['midpointCountry'];
$locName = $row['midpointName'];
$status = $row['status'];
$result->free();

if ($status == 'Ready') {
    echo '
    <script type="text/javascript">
        setMapCenterFromAddress(\'' . $midpointAddress . '\', true, \'' . addslashes($locName) . '\');
    </script>';
} else {
    echo '
    <script type="text/javascript">
        setMapCenterFromAddress(\'2315 Speedway, Austin, TX 78712-1512, United States\', false);
    </script>';
}


echo '
<div class="main_body">
    <div class="infoTreff">
        <div class="status">
            <h1>Status: ' . $status . '</h1>
            <table>
                <tr>
                    <th>User</th>
                    <th>Status</th>
                    <th>Reminder</th>
                </tr>';


while($row = $result->fetch_assoc()) {
    echo '<tr>
              <td>' . $row['email'] . '</td>
              <td>' . ($row['hasConfirmed'] == '1' ? 'Confirmed' : 'Not Confirmed') . '</td>';

    if ($row['hasConfirmed'] == '0') {
        echo '<td><button type="button" onclick="sendReminderEmail(' .$row['email'] . ');">Send Reminder Email</button>';
    } else {
        echo '<td></td>';
    }

    echo '</tr>';
}

$result->free();

echo $description .'
	</div>
</div>';

$connection->close();

include 'footer.php';