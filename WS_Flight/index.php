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
});

Flight::route('/api/event/', function($id){
        echo 'you called with post!' . $id;
});

Flight::route('POST /api/new/event', function(){

});

Flight::route('POST /api/new/participant', function(){

});

Flight::start();
?>
