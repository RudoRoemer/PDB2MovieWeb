<?php

  /*
    This class is for simply submitting a request from a user to view their request history of current and previous requests
    that have been made to that email address. The user must input their email and their secret code. This is not the most
    secure, as this is sent to them in plain text via email, but given as we opted for a lightweight log-in-free webapp, this
    is the best option to not allow any user to see the requests from any other user.
  */

  //get configs
  if (!$configs = parse_ini_file("../../config.conf")) {
    exit('{"status": "failure", "title": "Configuration error", "text": "Could not load config file on web server."}' );
  }

  //get http args and config file data
  $email = $_POST["email"];
  $secretCode = $_POST["secret_code"];

  $sqlServer = $configs["sqlServer"];
  $sqlUser = $configs["sqlUser"];
  $sqlPass = $configs["sqlPassword"];
  $sqlDB = $configs["sqlDB"];

  //establish connection to database and get all requests associated with user
  $conn_sql = mysqli_connect($sqlServer,$sqlUser, $sqlPass, $sqlDB) or die("Connection failed: " . mysql_connect_error());

  $stmt = $conn_sql->stmt_init();
  $stmt = $conn_sql->prepare("SELECT original_name, python_used, resolution, combi, multi, waters, threed, confs, freq, step, dstep, molList, modList, cutList, complete, req_id, filename
                              FROM Requests
                              INNER JOIN Users ON Requests.user_id = Users.user_id
                              WHERE Users.email=? AND Users.secret_code=?");
  $stmt->bind_param("si", $email, $secretCode);
  $stmt->execute();
  $sqlRes = $stmt->get_result();
  $rows = array();

  //for every request, add it to the array and then encode the array to a JSON format.
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
