<?php
  if (!$configs = parse_ini_file("../../config.conf")) {
    exit('{"status": "failure", "title": "Configuration error", "text": "Could not load config file on web server."}' );
  }

  echo sprint('{"renderer": %s, "pymolAllowed": %d}', $configs["renderer"], $configs["pymolAllowed"]);
?>
