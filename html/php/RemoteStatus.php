<?php

  include "RemoteConnection.php";

  if (!$configs = parse_ini_file("../../config.conf")) {
    exit('{"status": "failure", "title": "Configuration error", "text": "Could not load config file on web server."}' );
  }

  $conn_ssh = new RemoteConnection();

  if(is_string($conn_ssh->res)) {

    exit(sprintf('{"status": "failure", "title":"Offline", "text": "%s"}', json_decode($conn_ssh->res)->text ));
  }
  else {
    exit('{"status": "success", "title":"Online", "text": "Connected to Processing server securely."}');
  }

?>
