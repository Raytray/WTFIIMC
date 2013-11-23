<?php

include_once('dblogin.php');

$db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
if (mysqli_connect_error()) {
    echo "Can't connect!";
    echo "<br>" . mysqli_connect_error();
    return null;
}

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$location = $_POST['location'];
$can_drive = $_POST['can_drive'];
$seats = $_POST['seats'];
$event_id = $_POST['event_id'];
$start = date('Y-m-d H;i:s', strtotime($_POST['start_datetime']));
$end = date('Y-m-d H:i:s', strtotime($_POST['end_datetime']));

$sql="insert into participants (name, email, phone, location, can_drive, seats, event, start_datetime, end_datetime) values ('$name', '$email', '$phone', '$location', '$can_drive', '$seats', '$event_id', '$start', '$end')";

$result = mysqli_query($db_connection, $sql);
$sql = "SELECT name FROM participants WHERE event = " . $event_id;
$results = mysqli_query($db_connection, $sql);
echo '<ol>';
while($row = mysqli_fetch_array($results)) {
    echo '<li>' . $row['name'] . '</li>';
}
echo '</ol>';

mysqli_close($db_connection);

?>
