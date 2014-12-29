<?php

session_start();

$entry_Id = $_GET['entry_Id'];
include_once 'functions.php';

createHeader(array("style.css"), array());

$connection = connectMySql();
$result = $connection->query("SELECT entry_Id, user_Id, title, subject, rating, description, DATE_FORMAT(datetime, '%c-%d-%Y') AS entry_date
                                  FROM Entries
                                  WHERE entry_Id='$entry_Id'
                                  AND
                                  user_Id='".$_SESSION['user_Id']."'");

$row = $result->fetch_assoc();
$user_Id = $row['user_Id'];
$title = $row['title'];
$subject = $row['subject'];
$rating = get_rating($row['rating']);
$description = $row['description'];
$date = $row['entry_date'];
$result->free();

echo '
<div class="main_body">
    <div class="infoTreff">
            <h1>'.$title.'</h1>
            <table>
                <tr>
                    <th>Subject:</th>'.
                    '<td>'.$subject.'</td>'.'
                </tr>
                <tr>
                    <th>Date:</th>'.
                    '<td>'.$date.'</td>'.'
                </tr>
                <tr>
                    <th>How you felt:</th>'.
                    '<td>'.$rating.'</td>'.'
                </tr>
                <tr>
                    <th>How you remember it...</th>'.'
                </tr>';


    echo '<td></td>';

    echo '</tr>';

echo '      </table>';

echo $description .'
	</div>
</div>';

$connection->close();

include 'footer.php';