<?php
require 'flight/Flight.php';

function getEvents(){

}


Flight::route('/', function(){
        Flight::render('index', array('title' => 'Index'));
});

Flight::route('/participants', function(){
        Flight::render('participants', array('title' => 'Participants'));
});

Flight::route('/events', function(){
        Flight::render('list', array('title' => 'Events list'));
});

Flight::route('/events/@id', function($id){
        Flight::render('events', array('title' => 'Events', 'event_id' => $id));
});

Flight::route('GET /api/events', function(){
        include_once('php/dblogin.php');
        $db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
        if (mysqli_connect_error()) {
                echo "Can't connect!";
                echo "<br>" . mysqli_connect_error();
                return null;
        }
        $sql = "SELECT * FROM events";
        $results = mysqli_query($db_connection, $sql);

        $json = array();

        while($row = mysqli_fetch_array($results)) {
            $bus = array('id'=> $row['id'],
                         'name' => $row['name'],
                         'event_info'=> $row['event_info'],
                         'start_datetime' => $row['start_datetime'],
                         'end_datetime' => $row['end_datetime']);
            array_push($json, $bus);
        };

        mysqli_close($db_connection);

        $return = array('results' => $json);

        echo json_encode($return);
});

Flight::route('/api/event/@id', function($id){
        include_once('php/dblogin.php');
        $db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
        if (mysqli_connect_error()) {
                        echo "Can't connect!";
                        echo "<br>" . mysqli_connect_error();
                        return null;
        }
        $sql = "SELECT * FROM events where id=" . $id;
        $results = mysqli_query($db_connection, $sql);
        mysqli_close($db_connection);
});

Flight::route('POST /api/new/event', function(){
        include_once('php/dblogin.php');
        $db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
        if (mysqli_connect_error()) {
                echo "Can't connect!";
                echo "<br>" . mysqli_connect_error();
                return null;
        }
        $name = $_POST['name'];
        $eventinfo = $_POST['event_info'];
        $start = $_POST['start_datetime'];
        $end = $_POST['end_datetime'];

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

        $sql = "SELECT * FROM events";
        $results = mysqli_query($db_connection, $sql);
        mysqli_close($db_connection);
});

Flight::route('POST /api/new/participant', function(){
        include_once('php/dblogin.php');

        $db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
        if (mysqli_connect_error()) {
                echo "Can't connect!";
                echo "<br>" . mysqli_connect_error();
                return null;
        }

        $name = $_POST['name'];
        $can_drive = $_POST['can_drive'];
        $seats = $_POST['seats'];
        $event_id = $_POST['event_id'];
        $start = $_POST['start_datetime'];
        $end = $_POST['end_datetime'];
        $request = Flight::request();
        $data = $request->data;

        $sql="insert into participants (name, can_drive, seats, event, start_datetime, end_datetime) values ('$name', '$can_drive', '$seats', '$event_id', '$start', '$end')";

        $result = mysqli_query($db_connection, $sql);
        $sql = "SELECT name FROM participants WHERE event = " . $event_id;
        $results = mysqli_query($db_connection, $sql);
        mysqli_close($db_connection);
});

Flight::start();
?>
