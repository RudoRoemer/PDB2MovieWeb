<?php

  if (!$configs = parse_ini_file("../../config.conf")) {
    echo "{'status': 'failure', 'title': 'Cnfiguration error', 'text': 'Could not load config file on web server.'}";
  }

  $email = $_POST["email"];

  $sqlServer = $configs["sqlServer"];
  $sqlUser = $configs["sqlUser"];
  $sqlPass = $configs["sqlPassword"];
  $sqlDB = $configs["sqlDB"];

  $conn_sql = mysqli_connect($sqlServer,$sqlUser, $sqlPass, $sqlDB) or die("Connection failed: " . mysql_connect_error());

  $stmt = $conn_sql->stmt_init();
  $stmt = $conn_sql->prepare("SELECT filename, python_used, resolution, combi, multi, waters, threed, confs, freq, step, dstep, molList, modList, cutList
                              FROM Requests
                              INNER JOIN Users ON Requests.user_id = Users.user_id
                              WHERE Users.email=?;");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $sqlRes = $stmt->get_result();

  $rows = array();
  while($r = $sqlRes->fetch_assoc()) {
      $rows[] = $r;
  }

  $queryRes = json_encode($rows);
  echo $queryRes;
  mysqli_close($conn_ssh);


?>
