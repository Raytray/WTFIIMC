<?php

include_once('dblogin.php');

$db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
if (mysqli_connect_error()) {
    echo "Can't connect!";
    echo "<br>" . mysqli_connect_error();
    return null;
}
//echo "connect successful";
$name = $_POST['name'];
$can_drive = $_POST['can_drive'];
$seats = $_POST['seats'];
$event_id = $_POST['event_id'];
$start = $_POST['start_datetime'];
$end = $_POST['end_datetime'];

$sql="insert into participants (name, can_drive, seats, event, start_datetime, end_datetime) values ('$name', '$can_drive', '$seats', '$event_id', '$start', '$end')";

$result = mysqli_query($db_connection, $sql);

mysqli_close($db_connection);

?>
