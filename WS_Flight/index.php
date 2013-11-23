<?php
require 'flight/Flight.php';

Flight::route('/', function(){
        Flight::render('navbar', array('temp' => 'temp'), 'navbar');
        Flight::render('index', array('title' => 'Index'));
    });

Flight::route('/events', function(){
        Flight::render('navbar', array('temp' => 'temp'), 'navbar');
        Flight::render('list', array('title' => 'Events list'));
    });

Flight::route('/events/@id', function($id){
        Flight::render('navbar', array('temp' => 'temp'), 'navbar');
        Flight::render('events', array('title' => 'Events', 'event_id' => $id));
    });

Flight::route('/android', function(){
        Flight::render('navbar', array('temp' => 'temp'), 'navbar');
        Flight::render('android', array('title' => 'Android App'));
    });

Flight::route('/api', function(){
        Flight::render('navbar', array('temp' => 'temp'), 'navbar');
        Flight::render('api', array('title' => 'API'));
    });

Flight::route('GET /api/event(/(@id))', function($id = NULL){
        include_once('php/dblogin.php');
        $db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
        if (mysqli_connect_error()) {
                echo "Can't connect!";
                echo "<br>" . mysqli_connect_error();
                return null;
        }
        $sql = "";
        if ($id == NULL) {
            $sql = "SELECT * FROM events where start_datetime>=NOW() order by start_datetime ASC";
        }
        else {
            $sql = "SELECT * FROM events WHERE id = " . $id;
        }
        $results_events = mysqli_query($db_connection, $sql);

        $json = array();

        while($row = mysqli_fetch_array($results_events)) {

            $sql_participants = "SELECT name, can_drive, seats, start_datetime, end_datetime, created_datetime FROM participants WHERE event = " . $row['id'];

            $results_participants = mysqli_query($db_connection, $sql_participants);
            $participants = array();

            while($row_participants = mysqli_fetch_array($results_participants)){
                $participant_obj = array('name' => $row_participants['name'],
                                         'can_drive' => $row_participants['can_drive'],
                                         'seats' => $row_participants['seats'],
                                         'start_datetime' => $row_participants['start_datetime'],
                                         'end_datetime' => $row_participants['end_datetime'],
                                         'created_datetime' => $row_participants['created_datetime']);
                array_push($participants, $participant_obj);
            }

            $bus = array('id'=> $row['id'],
                         'name' => $row['name'],
                         'event_info'=> $row['event_info'],
                         'start_datetime' => $row['start_datetime'],
                         'end_datetime' => $row['end_datetime'],
                         'participants' => $participants);
            array_push($json, $bus);
        };

        mysqli_close($db_connection);

        $return = array('results' => $json);

        echo json_encode($return);
    });

Flight::route('POST /api/new/event', function(){
        include_once('php/dblogin.php');
        $db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
        if (mysqli_connect_error()) {
                echo "Can't connect!";
                echo "<br>" . mysqli_connect_error();
                return null;
        }
        $request = Flight::request();
        $data = $request->data;
        $name = $data->name;
        $eventinfo = $data->event_info;
        $start = date('Y-m-d H:i', strtotime($data->start_datetime));
        $end = date('Y-m-d H:i', strtotime($data->end_datetime));

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

        $request = Flight::request();
        $data = $request->data;
        $name = $data->name;
        $can_drive = $data->can_drive;
        $seats = $data->seats;
        $event_id = $data->event_id;
        $start = date('Y-m-d H:i', strtotime($data->start_datetime));
        $end = date('Y-m-d H:i', strtotime($data->end_datetime));

        $sql="insert into participants (name, can_drive, seats, event, start_datetime, end_datetime) values ('$name', '$can_drive', '$seats', '$event_id', '$start', '$end')";

        $result = mysqli_query($db_connection, $sql);
        mysqli_close($db_connection);
    });

Flight::start();
?>
