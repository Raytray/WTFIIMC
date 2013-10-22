<?php
require 'flight/Flight.php';

Flight::route('/', function(){
        Flight::render('index', array('title' => 'Index'));
});

Flight::route('/participants', function(){
        Flight::render('participants', array('title' => 'Participants'));
});

Flight::route('/events/@id', function($id){
        echo "events: " . $id;
});

Flight::start();
?>
