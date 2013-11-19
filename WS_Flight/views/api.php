<html>
    <head>
        <title><?php echo $title; ?>|WTFIIMC</title>
        <link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
        <link href="static/css/wtfiimc.css" rel="stylesheet">
        <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

    </head>

    <body>
        <div class="container">
            <?php echo $navbar; ?>
            <h3>GET plato.cs.virginia.edu/~cs4720f13cucumber/api/event(/(@id))</h3>
            <p>Get an event's participants information as well as general event infrmation with the optional argument of an event ID. Information is returned in json format. Example output can be found <a href="http://plato.cs.virginia.edu/~cs4720f13cucumber/api/event/1">here</a>
            </p>
            <br />
            <h3>GET wtfiimc.appspot.com/api/schedule/?id=@id</h3>
            <p>Get an event's participant's grouping. Riders are sorted with the best available driver. Algorithm favors fewer cars. Example output can be found <a href="http://wtfiimc.appspot.com/api/schedule/?id=1">here.</a>
            </p>
        </div>
    </body>
</html>
