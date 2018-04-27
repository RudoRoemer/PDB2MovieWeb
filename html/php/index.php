<?php

	if (!$configs = parse_ini_file("../../config.conf")) {
		endOp(jsonFormat("Failure","Cnfiguration error", "Could not load config file on web server."));
	}

	include "FileChecker.php";
	include "PdbChecker.php";
	include "PythonChecker.php";

	//make sure the user has agreed to terms of service on the server side.
	if (!$_POST["tos"] === true) {
		die("The Terms of Service were not accepted.");
	}

	$sha1Args = sha1($_POST["confs"] . $_POST["freq"] . $_POST["step"] . $_POST["dstep"] . $_POST["email"] . $_POST["modList"] . $_POST["molList"] . $_POST["cutList"] . $_POST["waters"] . $_POST["combi"] . $_POST["threed"] . $_POST["multiple"]);
	$sha1File = sha1_file($_FILES['pdbFile']['tmp_name']);
	$sha1Final = sha1($sha1Args . $sha1File);

 	$pdbFile = new PdbChecker($sha1Final);
	if ($pdbFile->didItPass() !== "Success"){
    endOp(jsonFormat("Failure", "Somethin has gone wrong", "The PDB file send did not pass authentication " . $pdbFile->didItPass())); //. $pdbFile->didItPass()));
	}
	$newLoc = $pdbFile->getTmpLocation();
	$pyFileUsed = file_exists($_FILES['pyFile']['tmp_name']);
	$pyToPass = 0;
	if ($pyFileUsed) {
		$pyToPass = 1;
  	$pyFile = new PythonChecker($sha1Final);
  	if ($pyFile->didItPass() !== "Success"){
      endOp(jsonFormat("Failure", "Something has gone wrong", ".py file: " . $pyFile->didItPass()));
		} else {
			$newLocPyth = $pyFile->getTmpLocation();
			move_uploaded_file($_FILES['pyFile']['tmp_name'], $pyFile->getTmpLocation());
		}
	}

	move_uploaded_file($_FILES['pdbFile']['tmp_name'], $pdbFile->getTmpLocation());

	//validates inputs, ends program if erroneous
                //move file to tmp location.

	switch (false) {
		case filter_var($_POST["confs"]				, FILTER_VALIDATE_INT);
			endOp(jsonFormat("Failure", "Something has gone wrong","Invalid value in configuration parameter."));
		case filter_var($_POST["freq"]				, FILTER_VALIDATE_INT);
			endOp(jsonFormat("Failure", "Something has gone wrong","Invalid value in frequency parameter."));
		case filter_var($_POST["step"]				, FILTER_VALIDATE_FLOAT);
			endOp(jsonFormat("Failure", "Something has gone wrong","Invalid value in random step parameter."));
		case filter_var($_POST["dstep"]				, FILTER_VALIDATE_FLOAT);
			endOp(jsonFormat("Failure", "Something has gone wrong","Invalid value in direct step parameter."));
		case filter_var($_POST["email"]				, FILTER_VALIDATE_EMAIL);
			endOp(jsonFormat("Failure", "Something has gone wrong","Invalid email"));
	}
	$invBool = "Invalid boolean.";
	if (filter_var($_POST["combi"], FILTER_VALIDATE_BOOLEAN) === NULL) {
		endOp(jsonFormat("Failure", "Something has gone wrong","invalid BOOL"));
	}
	if (filter_var($_POST["waters"], FILTER_VALIDATE_BOOLEAN) === NULL) {
		endOp(jsonFormat("Failure", "Something has gone wrong","invalid BOOL"));
	}
	if (filter_var($_POST["threed"], FILTER_VALIDATE_BOOLEAN) === NULL) {
		endOp(jsonFormat("Failure", "Something has gone wrong","invalid BOOL"));
	}
	if (filter_var($_POST["multiple"], FILTER_VALIDATE_BOOLEAN) === NULL) {
		endOp(jsonFormat("Failure", "Something has gone wrong","invalid BOOL"));
	}

	if (!preg_match('/^([A-Z0-9][A-Z0-9]?[A-Z0-9]?( ?))*$/', 	$_POST["molList"])) { endOp(jsonFormat("Failure", "Something has gone wrong", "Invalid keep list.")); }
	if (!preg_match('/^(([0-9])([0-9]?)( ?))*$/', 						$_POST["modList"])) { endOp(jsonFormat("Failure", "Something has gone wrong", "Invalid mode list.")); }
	if (!preg_match('/^([0-9].[0-9]+( ?))*$/',	 							$_POST["cutList"])) { endOp(jsonFormat("Failure", "Something has gone wrong", "Invalid cutoff list.")); }

	$file=file_get_contents( $newLoc );

	//script would have ended if any value is not sanitized, good to send.
	$name = ltrim($newLoc, $configs["localDirectory"] . "/php/pdb_tmp/");
	$pyName = ltrim($newLocPyth, $configs["localDirectory"] . "/php/pdb_tmp/");
	$origName = filter_var($_FILES['pdbFile']['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	$res = $_POST["res"];
	$waters = ($_POST["waters"] === "true" ? 1 : 0);
	$combi = ($_POST["combi"] === "true" ? 1 : 0);
	$multiple =($_POST["multiple"] === "true" ? 1 : 0);
	$threed =($_POST["threed"] === "true" ? 1 : 0);
	$fileKeep = ( $_POST["fileKeep"] === "true" ? 1 : 0);
	$confs = $_POST["confs"];
	$freq = $_POST["freq"];
	$step = $_POST["step"];
	$dstep = $_POST["dstep"];
	$email = $_POST["email"];
	$molList = $_POST["molList"];
	$modList = $_POST["modList"];
	$cutList = $_POST["cutList"];
	$conn_ssh;
	$remote_host = $configs["remoteHost"];
	$remote_host_fp = $configs["remoteHostFingerPrint"];
	$user = $configs["remoteUser"];
	$location = $configs["remoteDirectory"];
	$remoteScripts = $configs["remoteScripts"];
	$public_key = $configs["sshPublic"];
	$private_key = $configs["sshPrivate"];
	$localScripts = $configs["localScripts"];
	$thisServer = $configs["localHost"];

	$ssh_error = "SSH command failed. Error on the processing server side.";

	//establish connection with remote SCRTP computer
	if (!($conn_ssh = ssh2_connect($remote_host, 22,  array('hostkey'=>'ssh-rsa')))) {
		endOp(jsonFormat("Failure", "Something has gone wrong","Could not connect to server."));
	}

	$fingerprint = ssh2_fingerprint($conn_ssh, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);

	if (strcmp($remote_host_fp, $fingerprint) !== 0) {
		endOp(jsonFormat("Failure", "Something has gone wrong","cannot Identify Server."));
	}

	$auth = ssh2_auth_pubkey_file($conn_ssh, $user, $public_key, $private_key);
	if (!$auth) {
		endOp(jsonFormat("Failure", "Something has gone wrong","Authentication failure. Currently an issue with connecting to processing server, try again later."));
	}

	$output;
	$rawname = rtrim($name, '.pdb');
	//echo $newLoc . "\n";
	//echo "/storage/disqs/" . $user . "/pdb_tmp/" . $name . "\n";
	if (!(ssh2_scp_send($conn_ssh, $newLoc, $remoteScripts . "/pdb_tmp/" . $name ))) {
		endOp(jsonFormat("Failure", "Something has gone wrong","Error uploading pdb file to processing server."));
	}
	if ($pyFileUsed) {
		if (!(ssh2_scp_send($conn_ssh, $newLocPyth, $remoteScripts . "/pdb_tmp/" . $pyName ))) {
			endOp(jsonFormat("Failure", "Something has gone wrong","Error uploading python file to processing server. newLocPyth: " . $newLocPyth . " fullLoc: " . $location . $user . "/pdb_tmp/" . $pyName));
		}
	}
	//if the optional params are empty or equal spaces then add "NULL" so argument fits syntax of qsub on torque server whilst and for code to remove from qsub submission
	if ($molList == "") {
		$molList = "NULL";
	}
        if ($modList == "") {
                $modList = "NULL";
        }
        if ($cutList == "") {
                $cutList = "NULL";
        }

	//endOp("Request Sent. DB not connected stmt->getResult() undefined. requires more up-to-date PHP, will be sorted.");

	$sqlServer = $configs["sqlServer"];
	$sqlUser = $configs["sqlUser"];
	$sqlPass = $configs["sqlPassword"];
	$sqlDB = $configs["sqlDB"];

	//if connection fails, stop script
	$conn_sql = mysqli_connect($sqlServer,$sqlUser, $sqlPass, $sqlDB) or die("Connection failed: " . mysql_connect_error());

	$stmt = $conn_sql->stmt_init();
  $stmt = $conn_sql->prepare("SELECT user_id, secret_code FROM Users WHERE email=?;");
  $stmt->bind_param("s", $email);
  $stmt->execute();
	$sqlRes = $stmt->get_result();
	$row = $sqlRes->fetch_assoc();

	if (!$row) {
				$rand = mt_rand(100000, 999999);
				$stmt = $conn_sql->stmt_init();
        $stmt = $conn_sql->prepare("INSERT INTO Users (email, max_requests, user_id, current_requests, blacklisted, secret_code) VALUES (?, 3, NULL, 0, 0. ?);");
        $stmt->bind_param("s", $email, $rand);
        $stmt->execute();

        $userID = mysqli_stmt_insert_id($stmt);

	} else {

		$userID = $row["user_id"];
		$rand = $row["secret_code"];

	}

	$stmt = $conn_sql->prepare("SELECT secret_code FROM Users WHERE user_id=?");
	$stmt->bind_param("s", $userID);
	$stmt->execute();
	$fetchRes = $stmt->get_result();
	$sCode = $fetchRes->fetch_assoc()["secret_code"];

	$qsub_cmd = sprintf('cd %s && qsub -N %s -v LOC="%s",RETDIR="%s",NAME="%s",RES="%s",WATERS="%s",COMBI="%s",MULTIPLE="%s",THREED="%s",FILEKEEP="%s",CONFS="%s",FREQ="%s",STEP="%s",DSTEP="%s",EMAIL="%s",MOLLIST="%s",MODLIST="%s",CUTLIST="%s",CODE="%s",LOCALHOST="%s",ORIGNAME=%s,TIME="%s",PYNAME="%s" -q taskfarm %s/submit.pbs',
	$remoteScripts,
	$name,
	$remoteScripts,
	$localScripts, //sdasdasdas
	$name,
	$res,
	$waters,
	$combi,
	$multiple,
	$threed,
	$fileKeep,
	$confs,
	$freq,
	$step,
	$dstep,
	$email,
	$molList,
	$modList,
	$cutList,
	$sCode,
	$thisServer,
	$origName,
	date('d M y hh:mm:ss'),
	$pyName,
	$remoteScripts);

	//echo "\n" . $qsub_cmd . "\n";
	//while ($row = $sqlRes->fetch_assoc()) {
	//	print($row["email"]);
	//}

	$stmt1 = $conn_sql->stmt_init();
	//"SELECT Requests.req_id FROM Users INNER JOIN Requests ON Users.user_id = Requests.user_id WHERE Users.email=? AND Requests.filename=? AND Requests.resolution=? AND Requests.combi=? AND Requests.multi=? AND Requests.waters=? AND Requests.threed=? AND Requests.confs=? AND Requests.freq=? AND Requests.step=? AND Requests.dstep=? AND Requests.molList=? AND Requests.modList=? AND Requests.cutList=?;"
	//bind_param("sssiiiiiiddsss", $email, $rawname, $res, intval($combi), intval($multiple), intval($waters), intval($threed), $confs, $freq, $step, $dstep, $molList, $modList, $cutList);
	$stmt1 = $conn_sql->prepare("SELECT Requests.req_id, Users.max_requests, Users.current_requests FROM Requests INNER JOIN Users ON Requests.user_id = Users.user_id WHERE Users.email=?");
	$stmt1->bind_param("s", $email);
	$stmt1->execute();

	$stmtRes = $stmt1->get_result();
	$row = $stmtRes->fetch_assoc();

	$stmt = $conn_sql->stmt_init();
	$stmt = $conn_sql->prepare("SELECT current_requests, max_requests FROM Users WHERE user_id=?");
	$stmt->bind_param("s", $userID);
	$stmt->execute();

	$fetchRes = $stmt->get_result();
	$fetch = $fetchRes->fetch_assoc();
	$currReqs = $fetch["current_requests"];
	$maxReqs = $fetch["max_requests"];

	$stmt = $conn_sql->stmt_init();
	$stmt = $conn_sql->prepare("SELECT complete FROM Requests WHERE filename=? AND python_used=? AND resolution=? AND combi=? AND multi=? AND waters=? AND threed=? AND confs=? AND freq=? AND step=? AND dstep=? AND molList=? AND modList=? AND cutList=? ORDER BY req_id DESC LIMIT 1");
	$stmt->bind_param("sisiiiiiiddsss", $rawname, $pyFileUsed, $res, intval($combi), intval($multiple), intval($waters), intval($threed), $confs, $freq, $step, $dstep, $molList, $modList, $cutList);
	$stmt->execute();
	$fetchRes = $stmt->get_result();
	$complete = $fetchRes->fetch_assoc()["complete"];

	if (($maxReqs > $currReqs)) {

		if (is_null($complete) || $complete == 1) {

	    $stmt = $conn_sql->stmt_init();
	    $stmt = $conn_sql->prepare("UPDATE Users SET current_requests = current_requests + 0 WHERE user_id=?");
	    $stmt->bind_param("s", $userID);

	    $stmt2 = $conn_sql->stmt_init();
	    $stmt2 = $conn_sql->prepare("INSERT INTO Requests (filename, python_used, resolution, combi, multi, waters, threed, confs, freq, step, dstep, molList, modList, cutList, req_id, user_id, original_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, ?, ?)");
			$stmt2->bind_param("sisiiiiiiddsssis", $rawname, $pyFileUsed, $res, intval($combi), intval($multiple), intval($waters), intval($threed), $confs, $freq, $step, $dstep, $molList, $modList, $cutList, $userID, $origName);

			if ($stmt->execute() && $stmt2->execute()) {
				if (!(ssh2_exec($conn_ssh, $qsub_cmd))) {
	          endOp(jsonFormat("Failure", "Something has gone wrong","There was an error with your process. If you get this message, please email s.moffat.1@warwick.ac.uk"));
	      } else {

					$args = sprintf("%s %s '%s' %s %s %s %s %s %s %s %s '%s' '%s' '%s' '%s' %s",
													$origName,
													$pyToPass,
													$res,
													$combi,
													$multiple,
													$waters,
													$threed,
													$confs,
													$freq,
													$step,
													$dstep,
													$molList,
													$modList,
													$cutList,
													date('d M y h:m:s'),
													$sCode
					);
					echo "cd " . $localScripts . "; ./mailer.sh " . $email . " 'PDB2Movie: Your Request' accepted.txt NULL " . $args . "; cd -";
					shell_exec("cd " . $localScripts . "; ./mailer.sh " . $email . " 'PDB2Movie: Your Request' accepted.txt NULL " . $args . "; cd -") ;
					endOp(jsonFormat("Success", "Thank you for your submission", "" . ++$currReqs . "/" . $maxReqs . " of your daily requests."));

				}

      } else { endOp(jsonFormat("Failure", "Something has gone wrong","There was an error adding your request to the queue: " . mysqli_stmt_error($stmt2))); }

		} else { endOp(jsonFormat("Failure", "Something has gone wrong", "This requests is currently being processed. Please wait for this to be completed before requesting it again.")); }

	} else {
		$fff = jsonFormat("Failure", "Something has gone wrong","You have Reached your daily limit of " . $currReqs . "/" . $maxReqs . ". This will be reset at 00:00:00 GMT.");
		endOp($fff);
	}

	//echo print_r(posix_g	if () {
//etpwuid(posix_geteuid()));

	//ends program and deletes file.
	function endOp($msg) {
		global $newLoc;
		global $conn_ssh;
		try {
			unlink( $newLoc );
		} catch ( RuntimeException $e ) {}
		try {
			ssh2_exec($conn_ssh, 'echo "EXITING" && exit;');
			mysqli_close($conn_ssh);
		} catch ( RuntimeException $e ) {}
		die($msg);
	}

	function jsonFormat($outcome, $title, $details) {
		// used concatenated so that uses of the functions could have vars with the printf(), using a nest printf() ended up displaying length of string. don't judge me, just kidding, you already have if you've got this far into the code. If you think that's bad, theres a whole other "server-side" layer,
		$toRet = '{"outcome": "' . $outcome . '", "title": "' . $title . '", "text": "' . $details . '"}';
		return $toRet;
	}

?>
