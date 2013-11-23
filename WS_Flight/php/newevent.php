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
$eventinfo = $_POST['event_info'];
$start = date('Y-m-d H:i', strtotime($_POST['start_datetime']));
$end = date('Y-m-d H:i',strtotime($_POST['end_datetime']));

if(isset($name)){
    $ins_stmt = $db_connection->stmt_init();
    if($ins_stmt->prepare("insert into events (name, event_info, start_datetime, end_datetime) values (?, ?, ?, ?)")){
        $ins_stmt->bind_param("ssss", $name, $eventinfo, $start, $end);
        $ins_stmt->execute();
        $ins_stmt->close();
    }
}

$stmt = $db_connection->stmt_init();

$stmt->close();

$sql = "SELECT * FROM events where start_datetime>=NOW() order by start_datetime ASC";
$results = mysqli_query($db_connection, $sql);
echo '<ol>';
while($row = mysqli_fetch_array($results)) {
    echo '<li><a href=/~cs4720f13cucumber/events/' . $row['id'] . '>' . $row['name'] . '</li>';
                  }
echo '</ol>';
mysqli_close($db_connection);

?>
