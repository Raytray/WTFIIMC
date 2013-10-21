<html>
<head>
	<title>Profile</title>
	<link href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
	<script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
	<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

	<script>
		$(document).ready(function(){
			$("#submitevent").click(function(){
				$.ajax({
					url:'php/newevent.php',
					data:{name:$('#name').val(), event_info:$('#eventinfo').val(), date_start:$('#start').val(), date_end:$('#end').val()},
					success: function(data){
						$('#results').html(data);
						$('#name').val("");
						$('#eventinfo').val("");
						$('#start').val("");
						$('#end').val("");
					}
				});
			});
		
		});
	</script>
</head>

<body>

<div class="container">
	<h1>Who The Fox Is In My Car</h1>
	<p>
		<b>Create a new event:</b><br>
		Event name: <input id="name" type="text" /><br>
		Event description: <br>
		<textarea id="eventinfo" type="text"></textarea><br>
		Start Date: <input id="start" type="text" /><br>
		End Date: <input id="end" type="text" /><br>
		<button type="button" id="submitevent">Create Event!</button>
	</p>
	<div id="results"></div>
	
	
</div>
</body>

</html>