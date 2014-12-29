<?php

session_start();

include_once 'functions.php';
require 'lib/vendor/autoload.php';
use Mailgun\Mailgun;

if (!isset($_SESSION['user_Id']) || $_SESSION['user_Id'] == 0) {
    header("Location: index.php");
}

$connect = connectMySql();

$entry_Id = $connect->insert_id;

// Create the idHashes
$creatorIdHash = md5($meetingId . $_SESSION['user_Id']);
$mateIdHash = md5($meetingId . $mateUserId);

//print_r("<pre>".date('Y-m-d H:i:s', time())."</pre>");
// Create journal entry in Entries table
$connect->query("INSERT INTO Entries (user_Id, title, subject, rating, description, datetime)
                 VALUES ('" . $_SESSION['user_Id']. "', '" . $_POST['title'] . "', '" . $_POST['subject'] . "', '" . $_POST['rating'] . "', '" . $_POST['description'] . "', '". date('Y-m-d H:i:s') . "')");

//// Send emails
//$mg = new Mailgun("key-3g4koukbw35jwaa0ldtd32sqjzq-7948");
//$domain = "project";
//
//
//# Send confirmation email to creator
//$mg->sendMessage($domain,
//    array('from'    => 'Treff <noreply@project>',
//          'to'      => 'mars110110@aol.com',
//          'subject' => 'Confirmation for creating the Treff "' . $_POST['treffName'] . '"',
//          'text'    => "Thank you for using Treff! We hope your experience was simple and timely.\n\n" .
//                       "This is a confirmation email for the Treff you created. Below is a link to the meeting main page.\n" .
//                       "http://project/treff.php?idHash=$creatorIdHash\n\n" .
//                       "Happy Treffing,\n" .
//                       "The Treff Team"));


header("Location: /view_entries.php");