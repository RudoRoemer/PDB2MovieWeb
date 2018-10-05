<?php

/*
  A class for creating remote connection to the processing server. 
  This takes arguments from the configs to create and
  authenticate the connection before it is ready to be used. 
  The output is $this->res, a variable that will either
  be a SSH2 session object if all of the stages are passed, 
  or a JSON structure string variable that details which
  stage of initialisation was failed.
*/

class RemoteConnection {
    
    private $configs;
    private $conn_ssh;
    
    public function __construct() {
        
        $this->res;
        //get configs
        $configs = parse_ini_file("../../config.conf");
        
        //are the configs in the right location?
        if (!$configs) {
            $this->res = '{"status": "failure", "title": "Configuration error", "text": "Could not load config file on web server."}';
            return $this->res;
        }
        
        //have use instantiated the connection successfully?
        if (!($conn_ssh = ssh2_connect($configs['remoteHost'], 22,  array('hostkey'=>'ssh-rsa')))) {
            $this->res = '{"status": "failure", "title": "Something went wrong", "text": "Could not connect to processing server."}';
            return $this->res;
        }
        
        //get fingerprint from connection
        $fingerprint = ssh2_fingerprint($conn_ssh, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);
        
        //does it match the configs for the current fingerprint (stops man-in-middle attacks)
        
        if (strcmp($configs['remoteHostFingerPrint'], $fingerprint) !== 0) {
            $this->res = '{"status": "failure", "title": "Something went wrong", "text": "Fingerprint did not match processing server\'s. Fingerprint supplied: '+$fingerprint+'"}';
            return $this->res;
        }
        
        //send public key, use remote public key with local pricate key on the connection
        $auth = ssh2_auth_pubkey_file($conn_ssh, $configs['remoteUser'], $configs['sshPublic'], $configs['sshPrivate']);
        if (!$auth) {
            $this->res = '{"status": "failure", "title": "Something went wrong", "text": "Could not authenticate processing server."}';
            return $this->res;
        }
        
        //made it this far? then the connection has been authenticated.
        $this->res = $conn_ssh;
        return $this->res;
        
    }
    
}

?>
