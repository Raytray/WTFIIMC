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
