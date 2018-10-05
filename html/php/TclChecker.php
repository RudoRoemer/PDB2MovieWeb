<?php

class TclChecker extends FileChecker {
    
    public function __construct($uniqueFileName) {
        
        $this->hashedName = $uniqueFileName;
        $this->file = "pyFile";
        $this->mimeTypes = array("application/x-python-code", "text/x-python",  "text/plain");
        $this->ext = "py";
        return $this->check();
        
    }
    
    public function check() {
        
        global $configs;
        $this->baseCheck();
        
        if ($this->checkRes === "Success") {
            
            $file=file_get_contents($_FILES[$this->file]['tmp_name']);
            $comp=file_get_contents("../../tcl-whitelist.txt");
            $bloc=file_get_contents("../../tcl-blacklist.txt");
            $remove = "\n";
            $split = explode($remove, $file);
            $csplt = explode($remove, $comp);
            $bsplt = explode($remove, $bloc);
            $acc = 0;
            $blackListFlag = false;
            $match = true;
            
            foreach ($split as $str) {
                
                acc++;
                
                if (substr($str, 0, 1) === "#" || $str == "") {
                    continue 1;
                }
                
                if ($configs["useBlackList"] === "1") {
                    foreach($bsplt as $bl) {
                        
                        $bl = preg_replace("/[^a-zA-Z0-9(),;:._# ]+/", "", $bl);
                        
                        
                        if (strpos($str, $bl) !== false ) {
                            
                            $blackListFlag = true;
                            break 2;
                            
                        }
                        
                    }
                    
                }
                
                if ($configs["useWhiteList"] === "1") {
                    
                    foreach($csplt as $wl) {
                        
                        $tmp = strpos($wl, "()");
                        $wl = preg_replace("/[^a-zA-Z0-9,;:._ ]+/", "", $wl);
                        if (strpos($str, $wl) !== false && $wl !== null) { $match = true; break 1; }
                        
                    }
                    
                    if (!$match || $blackListFlag) {
                        $this->checkRes = "command at line " . --$acc . " is black listed, reads:\n\n" . $split[$acc];
                    }
                    
                }
            }
        }
        
    }
    
}

?>
