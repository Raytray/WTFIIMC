<?php
	
	include_once('dblogin.php');
	
	$db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
	if (mysqli_connect_error()) {
		echo "Can't connect!";
		echo "<br>" . mysqli_connect_error();
		return null;
	}
	//echo "connect successful";
	$name = $_GET['name'];
	$eventinfo = $_GET['event_info'];
	$start = $_GET['date_start'];
	$end = $_GET['date_end']; 
	
	if(isset($name)){
		$ins_stmt = $db_connection->stmt_init();
		if($ins_stmt->prepare("insert into events (name, event_info, start_datetime, end_datetime) values (?, ?, ?, ?)")){
			$ins_stmt->bind_param("ssss", $name, $eventinfo, $start, $end);
			$ins_stmt->execute();
			$ins_stmt->close();
		}
	}
	
	$stmt = $db_connection->stmt_init();
	
	/*if($stmt->prepare("SELECT task_id, task_desc, status from todo order by status")) {
		$stmt->execute();
		$stmt->bind_result($id, $task, $status);
		while($stmt->fetch()) {
			echo '<button type="button" class="del" id="' . $id . '">x</button>';
			if ($status==0){
				echo '<button type="button" class="check" id="' . $id . '" status="0">&#x2713;</button>';
				echo $task . "<br>\n";
			}
			else{
				echo '<button type="button" class="check" id="' . $id . '" status="1">&#x25a2;</button>';
				echo "<del>" . $task . "</del><br>\n";
			}
			
		}
	}*/
	
	$stmt->close();
	
	$db_connection->close();
	
?>