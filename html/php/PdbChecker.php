<?php

  /*
    checker for the pdb file. This will check for any illegal chars within the file.
    An exception is given to the REMARK command, as this is treated as regular text.
  */

  class PdbChecker extends FileChecker {

    public function __construct($uniqueFileName) {

      $this->hashedName = $uniqueFileName;
      $this->file = "pdbFile";
      $this->mimeTypes = array("chemical/x-pdb", "text/plain");
      $this->ext = "pdb";
      return $this->check();

    }

    public function check() {

      $this->baseCheck();

      if ($this->checkRes === "Success") {

        //split text lines in file into array and loop
      $file=file_get_contents( $_FILES[$this->file]['tmp_name'] /*$this->getTmpLocation()*/ );
        $remove = "\n";
        $split = explode($remove, $file);
        $lCount = 1;
        foreach ($split as $str) {

          //checks each line for unauthorised chars. remark statements are always treated as text and can have special characters in them
          $san = filter_var($str, FILTER_SANITIZE_SPECIAL_CHARS);
          if ($san !== $str && substr($str,0,6) !== "REMARK" && strpos($str, "'") !== false) {
            if (strpos($str, "'") !== false && (strpos($str, "HETNAM") === false AND strpos($str, "HETATM") === false)) {
              $hasPos = strpos($str, "'");
              $isHet = strpos($str, "HETNAM");
              $this->checkRes = sprintf(". At least one unexpected character found at line %s of .pdb file. hasApos: %s isHet: %s str: %s", $lCount,  $hasPos, $isHet, $str);
              break;
            }
          }

          //returns the line the illegal character appeared on.
          $lCount++;/**/

        }
      }
    }
  }
?>
