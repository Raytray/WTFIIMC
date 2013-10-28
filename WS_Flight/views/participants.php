<html>
    <head>
        <title><?php echo $title; ?>|WTFIIMC</title>
        <link href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
        <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        <link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

        <script>
            $(document).ready(function(){
                $("#submitevent").click(function(){
                    $.ajax({
                        method: 'POST',
                        url: 'php/newparticipant.php',
                        data: $('#participants_insert').serialize(),
                        success: function(data){
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
                <b>Create a new participant:</b><br>
                <form id="participants_insert">
                Participants name: <input name="name" type="text" /><br>
                Can drive?: <input type="checkbox" name="can_drive" /><br>
                Seats open?: <input type="text" name="seats" /><br>
                Event: <select name="event_id">
                        <?php
                          include_once('dblogin.php');
                          $db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
                          if (mysqli_connect_error()) {
                              echo "Can't connect!";
                              echo "<br>" . mysqli_connect_error();
                              return null;
                          }
                          $sql = "select id, name from events";
                          $results = mysqli_query($db_connection, $sql);

                          while($row = mysqli_fetch_array($results)) {
                              echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                          }
                          mysqli_close($db_connection);
                        ?>
                </select>
                <br>
                Start Date: <input name="start_datetime" type="text" /><br>
                End Date: <input name="end_datetime" type="text" /><br>
                <input type="submit" id="submitevent" value="Add Participant!">
                </form>
            </p>
            <div id="results"></div>
        </div>
    </body>
</html>
