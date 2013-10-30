<?php
require 'flight/Flight.php';

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
        $db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
        if (mysqli_connect_error()) {
                echo "Can't connect!";
                echo "<br>" . mysqli_connect_error();
                return null;
        }
        $sql = "SELECT * FROM events";
        $results = mysqli_query($db_connection, $sql);
        echo json_encode($results);
});

Flight::route('/api/event/@id', function($id){
        echo 'you called with post!' . $id;
});

Flight::route('POST /api/new/event', function(){

});

Flight::route('POST /api/new/participant', function(){

});

Flight::start();
?>
