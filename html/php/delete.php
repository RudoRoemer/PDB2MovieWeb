<?php

  /*
    This class allows for the removal of currently active requests. To do this, connection
    between the database and the processing server to the web server need to be
    established. The database side is pretty simple, it takes a req id, finds it, and
    deletes the entry. The deletion from the processing server's queue system requires a
    bash script to be ran upon to find the Job ID it was given with the filename, and then
    stop the it from processing before deleting any generated files.
  */

  include "RemoteConnection.php";

  if (!$configs = parse_ini_file("../../config.conf")) {
    exit('{"status": "failure", "title": "Configuration error", "text": "Could not load config file on web server."}' );
  }

  //establish connection
  $conn_ssh = new RemoteConnection();

  if (is_string($conn_ssh->res)) {
    die($conn_ssh->res);
  }

  //sanitise http args
  $reqID = intval($_POST['reqID']);
  $filename = $_POST['filename'];

  //get config args
  $sqlServer = $configs["sqlServer"];
	$sqlUser = $configs["sqlUser"];
	$sqlPass = $configs["sqlPassword"];
	$sqlDB = $configs["sqlDB"];
  $remoteScripts = $configs["remoteScripts"];

  //establish database connection and execute deletion of entry.
  $conn_sql = mysqli_connect($sqlServer,$sqlUser, $sqlPass, $sqlDB) or die("Connection failed: " . mysql_connect_error());

	$stmt = $conn_sql->stmt_init();
  $stmt = $conn_sql->prepare("DELETE FROM Requests WHERE req_id=?;");
  $stmt->bind_param("i", $reqID);
  $stmt->execute();

  //if deletion from database was successful, execute bashs script on the processing server
  if (mysqli_affected_rows($conn_sql) > 0) {
    if(!ssh2_exec($conn_ssh->res, 'cd ' . $remoteScripts . '; ./remove.sh ' . $filename)) { die('{"status": "Success", "text": "This has been deleted."}'); }
    die('{"status": "Success", "text": "This has been deleted."}');
  } else {
    die('{"status": "failure", "text": "Your request failed to be deleted"}');
  }

?>
