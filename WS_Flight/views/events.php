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
                        url: '../php/newparticipant.php',
                        data: $('#participants_insert').serialize(),
                        success: function(data){
                           $("#participants_insert")[0].reset();
                           $("#current_participants").html(data);
                       }
                    });
                    return false;
                });
            });
        </script>

        <link href="../static/css/wtfiimc.css" rel="stylesheet">
    </head>

    <body>

        <div class="container">
            <h1>Who The Fox Is In My Car</h1>
            <div class="left_box">
                <p>
                    <b>Add a new participant to event!</b><br>
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
                              $sql = "SELECT id, name FROM events WHERE id = " . $event_id;
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
                        <button type="button" class="btn btn-defualt" id="submitevent">Add Participant!</button>
                    </form>
                </p>
            </div>
            <div class="right_box">
                <p>
                    <b>Current participants:</b><br>
                    <div id="current_participants">
                        <?php
                                  include_once('dblogin.php');
                          $db_connection = new mysqli($SERVER, $USER, $PASSWORD, $DB);
                          if (mysqli_connect_error()) {
                              echo "Can't connect!";
                              echo "<br>" . mysqli_connect_error();
                              return null;
                          }
                          $sql = "SELECT name FROM participants WHERE event = " . $event_id;
                          $results = mysqli_query($db_connection, $sql);
                          echo '<ol>';
                          while($row = mysqli_fetch_array($results)) {
                              echo '<li>' . $row['name'] . '</li>';
                          }
                          echo '</ol>';
                          mysqli_close($db_connection);
                        ?></div>
            </div>
        </div>
    </body>
</html>
