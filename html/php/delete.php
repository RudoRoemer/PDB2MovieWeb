<?php

  include "RemoteConnection.php";

  $conn_ssh = new RemoteConnection();

  if (is_string($conn_ssh)) {
    die($conn_ssh);
  } else {
    var_dump($conn_ssh);
  }

  #echo "FILENAME=" . $_POST["filename"] . "\n";
  #echo "REQ=" . $_POST["reqID"] . "\n";
  #var_dump($conn_ssh);

?>
