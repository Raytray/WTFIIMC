<?php
require 'flight/Flight.php';

Flight::route('/', function(){
        Flight::render('index', array('title' => ''));
});

Flight::route('/event', function(){
        echo "Events";
});

Flight::start();
?>
