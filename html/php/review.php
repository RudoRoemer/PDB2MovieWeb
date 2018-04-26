<?php

  if (!$configs = parse_ini_file("../../config.conf")) {
    echo '{"status": "failure", "title": "Configuration error", "text": "Could not load config file on web server."}';
  }

  $email = $_POST["email"];
  $secretCode = $_POST["secret_code"];

  $sqlServer = $configs["sqlServer"];
  $sqlUser = $configs["sqlUser"];
  $sqlPass = $configs["sqlPassword"];
  $sqlDB = $configs["sqlDB"];

  $conn_sql = mysqli_connect($sqlServer,$sqlUser, $sqlPass, $sqlDB) or die("Connection failed: " . mysql_connect_error());

  $stmt = $conn_sql->stmt_init();
  $stmt = $conn_sql->prepare("SELECT original_name, python_used, resolution, combi, multi, waters, threed, confs, freq, step, dstep, molList, modList, cutList, complete
                              FROM Requests
                              INNER JOIN Users ON Requests.user_id = Users.user_id
                              WHERE Users.email=? AND Users.secret_code=?");
  $stmt->bind_param("si", $email, $secretCode);
  $stmt->execute();
  $sqlRes = $stmt->get_result();
  $rows = array();
  while($r = $sqlRes->fetch_assoc()) {
      $rows[] = $r;
  }
  if (!empty($rows)) {
    $queryRes = json_encode($rows);
  } else {
    $queryRes = '{"status": "failure", "title": "Something has gone wrong.", "text": "Email or secret code incorrect."}';
  }

  echo $queryRes;
  mysqli_close($conn_ssh);

?>
