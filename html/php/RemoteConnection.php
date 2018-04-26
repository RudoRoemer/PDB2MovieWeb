<?php

  class RemoteConnection {

    private $configs;
    private $conn_ssh;

    public function __construct() {

      $this->res;
      $configs = parse_ini_file("../../config.conf");

      if (!$configs) {
        $this->res = '{"status": "failure", "title": "Configuration error", "text": "Could not load config file on web server."}';
        return $this->res;
      }

      if (!($conn_ssh = ssh2_connect($configs['remoteHost'], 22,  array('hostkey'=>'ssh-rsa')))) {
        $this->res = '{"status": "failure", "title": "Something went wrong", "text": "Could not connect to processing server."}';
        return $this->res;
      }

    	$fingerprint = ssh2_fingerprint($conn_ssh, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);

    	if (strcmp($configs['remoteHostFingerPrint'], $fingerprint) !== 0) {
    		$this->res = '{"status": "failure", "title": "Seomthing went wrong", "text": "Fingerprint did not match processing server\'s."}';
        return $this->res;
      }

    	$auth = ssh2_auth_pubkey_file($conn_ssh, $configs['remoteUser'], $configs['sshPublic'], $configs['sshPrivate']);
    	if (!$auth) {
        $this->res = '{"status": "failure", "title": "Something went wrong", "text": "Could not authenticate processing server."}';

      }

      $this->res = $conn_ssh;
      return $this->res;

    }

  }

?>
