<?php
require 'flight/Flight.php';

Flight::route('/', function(){
    include('php/index.php');
});

Flight::route('/event', function(){
	echo "Events";
});

Flight::start();
?>
