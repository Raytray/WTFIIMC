<html>
    <head>
        <title><?php echo $title; ?>|WTFIIMC</title>
        <link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
        <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function(){
                $("#submitevent").click(function(){
                    $.ajax({
                        method: 'POST',
                        url: 'php/newevent.php',
                        data: $('#event_insert').serialize(),
                        success: function(data){
                            $('#results').html(data);
                            $('#name').val("");
                            $('#event_info').val("");
                            $('#start_datetime').val("");
                            $('#end_datetime').val("");
                            alert("Success!");
                       }
                    });
                    return false;
                });
            });
        </script>
    </head>

    <body>

        <div class="container">
            <h1>Who The Fox Is In My Car</h1>
            <p>
                <b>Create a new event:</b><br>
                <form id="event_insert">
                Event name: <input name="name" type="text" /><br>
                Event description: <br>
                <textarea style='margin: 0px; width: 262px; height: 130px;' name="event_info" type="text"></textarea><br>
                Start Date: <input name="start_datetime" type="text" /><br>
                End Date: <input name="end_datetime" type="text" /><br>
                <button type="button" class="btn btn-defualt" id="submitevent">Create Event!</button>
                </form>
            </p>
            <div id="results"></div>
            <p><b>Current events:</b>
                <?php
                  include_once('dblogin.php');
                  $db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
                  if (mysqli_connect_error()) {
                      echo "Can't connect!";
                      echo "<br>" . mysqli_connect_error();
                      return null;
                  }
                  $sql = "SELECT * FROM events";
                  $results = mysqli_query($db_connection, $sql);
                  echo '<ol>';
                  while($row = mysqli_fetch_array($results)) {
                      echo '<li><a href=/~cs4720f13cucumber/events/' . $row['id'] . '>' . $row['name'] . '</li>';
                  }
                  echo '</ol>';
                  mysqli_close($db_connection);
                ?>
            </p>


        </div>
    </body>

</html>
