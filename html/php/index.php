<?php

	/*
		this is the main section of code that allows users to make their request. It sanitizes the user's
		input, checks to see if the user is pre-existing on the database, associate the new data with the
		given email address, and send the request to the processing server.
	*/

	//Essentials are check before anything is processed
	if (!$configs = parse_ini_file("../../config.conf")) {
		endOp(jsonFormat("Failure","Cnfiguration error", "Could not load config file on web server."));
	}

	include "FileChecker.php";
	include "PdbChecker.php";
	include "PythonChecker.php";
	include "RemoteConnection.php";

	if (!$_POST["tos"] === true) {
		die("The Terms of Service were not accepted.");
	}

	//collisions possible with this - astronomically low chance for two file to exist in the same timeframe to affect each other
	$sha1Args = sha1($_POST["confs"] . $_POST["freq"] . $_POST["step"] . $_POST["dstep"] . $_POST["email"] . $_POST["modList"] . $_POST["molList"] . $_POST["cutList"] . $_POST["waters"] . $_POST["combi"] . $_POST["threed"] . $_POST["multiple"]);
	$sha1File = sha1_file($_FILES['pdbFile']['tmp_name']);
	$sha1Final = sha1($sha1Args . $sha1File);

	//authenticate .pdb file
 	$pdbFile = new PdbChecker($sha1Final);
	if ($pdbFile->didItPass() !== "Success"){
    endOp(jsonFormat("Failure", "Somethin has gone wrong", "The PDB file send did not pass authentication " . $pdbFile->didItPass())); //. $pdbFile->didItPass()));
	}
	//location of the file for when it needs to be moved to processing server
	$newLoc = $pdbFile->getTmpLocation();

	//is a video file used? if not, set pyToPass to false, if so, do a similar thing that was done for .pdb file
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

	//RegEx's for more dynamic input of lists
	if (!preg_match('/^([A-Z0-9][A-Z0-9]?[A-Z0-9]?( ?))*$/', 	$_POST["molList"])) { endOp(jsonFormat("Failure", "Something has gone wrong", "Invalid keep list.")); }
	if (!preg_match('/^(([0-9])([0-9]?)( ?))*$/', 						$_POST["modList"])) { endOp(jsonFormat("Failure", "Something has gone wrong", "Invalid mode list.")); }
	if (!preg_match('/^([0-9].[0-9]+( ?))*$/',	 							$_POST["cutList"])) { endOp(jsonFormat("Failure", "Something has gone wrong", "Invalid cutoff list.")); }

	//script would have ended if any value is not sanitized, good to send.
	$name = basename($newLoc);
	$rawname = basename($name, ".pdb");
	//$name = ltrim($newLoc, $configs["localDirectory"] . "/php/pdb_tmp/");
	$pyName = basename($newLocPyth);
	//$pyName = ltrim($newLocPyth, $configs["localDirectory"] . "/php/pdb_tmp/");
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
	$remote_host = $configs["remoteHost"];
	$remote_host_fp = $configs["remoteHostFingerPrint"];
	$user = $configs["remoteUser"];
	$location = $configs["remoteDirectory"];
	$remoteScripts = $configs["remoteScripts"];
	$public_key = $configs["sshPublic"];
	$private_key = $configs["sshPrivate"];
	$localScripts = $configs["localScripts"];
	$thisServer = $configs["localHost"];

	$conn_ssh = new RemoteConnection();

	//if conn_ssh is a string, it failed to connect, contents of string give JSON response of error.
  if (is_string($conn_ssh->res)) {
    die($conn_ssh->res);
  }

	//$rawname = rtrim($name, '.pdb');
	//send files to processing side
	if (!(ssh2_scp_send($conn_ssh->res, $newLoc, $remoteScripts . "/pdb_tmp/" . $name ))) {
		endOp(jsonFormat("Failure", "Something has gone wrong","Error uploading pdb file to processing server."));
	}
	if ($pyFileUsed) {
		if (!(ssh2_scp_send($conn_ssh->res, $newLocPyth, $remoteScripts . "/pdb_tmp/" . $pyName ))) {
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

	//get SQL configs
	$sqlServer = $configs["sqlServer"];
	$sqlUser = $configs["sqlUser"];
	$sqlPass = $configs["sqlPassword"];
	$sqlDB = $configs["sqlDB"];

	//if connection fails, stop script
	$conn_sql = mysqli_connect($sqlServer,$sqlUser, $sqlPass, $sqlDB) or die("Connection failed: " . mysql_connect_error());

	//get User id
	$stmt = $conn_sql->stmt_init();
  $stmt = $conn_sql->prepare("SELECT user_id, secret_code FROM Users WHERE email=?;");
  $stmt->bind_param("s", $email);
  $stmt->execute();
	$sqlRes = $stmt->get_result();
	$row = $sqlRes->fetch_assoc();

	//if no user id was received, create new user
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

	//get secret code for the to-send emails
	$stmt = $conn_sql->prepare("SELECT secret_code FROM Users WHERE user_id=?");
	$stmt->bind_param("s", $userID);
	$stmt->execute();
	$fetchRes = $stmt->get_result();
	$sCode = $fetchRes->fetch_assoc()["secret_code"];

	//all values at this point should be sanitized, format command for the processing server
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

	//get current and max requests from user
	$stmt = $conn_sql->stmt_init();
	$stmt = $conn_sql->prepare("SELECT current_requests, max_requests FROM Users WHERE user_id=?");
	$stmt->bind_param("s", $userID);
	$stmt->execute();
	$fetchRes = $stmt->get_result();
	$fetch = $fetchRes->fetch_assoc();
	$currReqs = $fetch["current_requests"];
	$maxReqs = $fetch["max_requests"];

	//get completion status from latest file under same hashed filename
	$stmt = $conn_sql->stmt_init();
	$stmt = $conn_sql->prepare("SELECT complete FROM Requests WHERE filename=? AND python_used=? AND resolution=? AND combi=? AND multi=? AND waters=? AND threed=? AND confs=? AND freq=? AND step=? AND dstep=? AND molList=? AND modList=? AND cutList=? ORDER BY req_id DESC LIMIT 1");
	$stmt->bind_param("sisiiiiiiddsss", $rawname, $pyFileUsed, $res, intval($combi), intval($multiple), intval($waters), intval($threed), $confs, $freq, $step, $dstep, $molList, $modList, $cutList);
	$stmt->execute();
	$fetchRes = $stmt->get_result();
	$complete = $fetchRes->fetch_assoc()["complete"];

	//are they under their request limit?
	if (($maxReqs > $currReqs)) {

		//if the their no such previous request in the history, or has it been fully completed?
		if (is_null($complete) || $complete == 1) {

			//increment user's daily requests
	    $stmt = $conn_sql->stmt_init();
	    $stmt = $conn_sql->prepare("UPDATE Users SET current_requests = current_requests + 0 WHERE user_id=?");
	    $stmt->bind_param("s", $userID);

			//insert their latest request
	    $stmt2 = $conn_sql->stmt_init();
	    $stmt2 = $conn_sql->prepare("INSERT INTO Requests (filename, python_used, resolution, combi, multi, waters, threed, confs, freq, step, dstep, molList, modList, cutList, req_id, user_id, original_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, ?, ?)");
			$stmt2->bind_param("sisiiiiiiddsssis", $rawname, $pyFileUsed, $res, intval($combi), intval($multiple), intval($waters), intval($threed), $confs, $freq, $step, $dstep, $molList, $modList, $cutList, $userID, $origName);

			//if database changes succeed
			if ($stmt->execute() && $stmt2->execute()) {
				//submit to the processing server
				if (!(ssh2_exec($conn_ssh->res, $qsub_cmd))) {
	          endOp(jsonFormat("Failure", "Something has gone wrong","There was an error with your process. If you get this message, please email s.moffat.1@warwick.ac.uk"));
	      } else {
					//format email args
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
					//send first email to show the process has been accepted.
					shell_exec("cd " . $localScripts . "; ./mailer.sh " . $email . " 'PDB2Movie: Your Request' accepted.txt NULL " . $args . "; cd -") ;
					endOp(jsonFormat("Success", "Thank you for your submission", "" . ++$currReqs . "/" . $maxReqs . " of your daily requests."));

				}

      } else { endOp(jsonFormat("Failure", "Something has gone wrong","There was an error adding your request to the queue: " . mysqli_stmt_error($stmt2))); }

		} else { endOp(jsonFormat("Failure", "Something has gone wrong", "This requests is currently being processed. Please wait for this to be completed before requesting it again.")); }

	} else {
		$fff = jsonFormat("Failure", "Something has gone wrong","You have Reached your daily limit of " . $currReqs . "/" . $maxReqs . ". This will be reset at 00:00:00 GMT.");
		endOp($fff);
	}

	//ends program and deletes file.
	function endOp($msg) {
		global $newLoc;
		global $conn_ssh;
		try {
			unlink( $newLoc );
		} catch ( RuntimeException $e ) {}
		try {
			//close connections
			ssh2_exec($conn_ssh->res, 'echo "EXITING" && exit;');
			mysqli_close($conn_ssh->res);
		} catch ( RuntimeException $e ) {}
		die($msg);
	}

	function jsonFormat($outcome, $title, $details) {
		//formats simple jason response. Use of this function ensures consistent JSON response structure.
		$toRet = '{"outcome": "' . $outcome . '", "title": "' . $title . '", "text": "' . $details . '"}';
		return $toRet;
	}

?>
