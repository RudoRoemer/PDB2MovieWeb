<?php

  include "RemoteConnection.php";

  if (!$configs = parse_ini_file("../../config.conf")) {
    exit('{"status": "failure", "title": "Configuration error", "text": "Could not load config file on web server."}' );
  }

  $conn_ssh = new RemoteConnection();

  if (is_string($conn_ssh->res)) {
    die($conn_ssh->res);
  }

  $reqID = intval($_POST['reqID']);
  $filename = $_POST['filename'];

  $sqlServer = $configs["sqlServer"];
	$sqlUser = $configs["sqlUser"];
	$sqlPass = $configs["sqlPassword"];
	$sqlDB = $configs["sqlDB"];
  $remoteScripts = $configs["remoteScripts"];

  $conn_sql = mysqli_connect($sqlServer,$sqlUser, $sqlPass, $sqlDB) or die("Connection failed: " . mysql_connect_error());

	$stmt = $conn_sql->stmt_init();
  $stmt = $conn_sql->prepare("DELETE FROM Requests WHERE req_id=?;");
  $stmt->bind_param("i", $reqID);
  $stmt->execute();

  if (mysqli_affected_rows($conn_sql) > 0) {
    var_dump($conn_ssh);
    if(!ssh2_exec($conn_ssh->res, $remoteScripts . '/remove.sh ' . $filename)) { die('{"status": "Success", "text": "This has been deleted."}'); }
    die('{"status": "Success", "text": "This has been deleted."}');
  } else {
    die('{"status": "failure", "text": "Your request failed to be deleted"}');
  }

?>
