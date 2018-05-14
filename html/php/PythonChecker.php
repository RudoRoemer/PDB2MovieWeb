<?php

  /*
    the python file checker. This sanitizes the given file to work within the FREEMOL
    extension for the program PyMOL. See bellow for TODO and concerns.
  */

  class PythonChecker extends FileChecker {

    public function __construct($uniqueFileName) {

      $this->hashedName = $uniqueFileName;
      $this->file = "pyFile";
      $this->mimeTypes = array("application/x-python-code", "text/x-python",  "text/plain");
      $this->ext = "py";
      return $this->check();

    }

    public function check() {

      $res = $this->baseCheck();

      if ($res === "Success") {
        $file=file_get_contents( $this->getTmpLocation() );
        $remove = "\n";
        $split = explode($remove, $file);
        $lCount = 1;
        foreach ($split as $str) {
          /*
            if (cmd) {
              if ($str.includes(".set_view(")) {
                if ($str.includes(")")) {
                  $externalFlag = false
                  $cmdParams=rtrim(($str, "cmd.set_view(")).explode(",")
                  if (!(regEx.test($cmdParams, *FIT FORMAT: signed float, ignore spacing*))) FAILURE
                } else {

                }
              }
              else if ($str.includes(".color(")) standardCheck((rtrim($str,"cmd.color(")).explode(","))
              else if ($str.includes(".show(")) standardCheck((rtrim($str,"cmd.show(")).explode(","))
              else if ($str.includes(".select(")) standardCheck((rtrim($str,"cmd.select(")).explode(","))
              else FAILURE; break;

            } else {
              if ($externalFlag === true) {
                $cmdParams=rtrim(($str, "cmd.set_view(")).explode(",")
                if (!(regEx.test($cmdParams, *FIT FORMAT: signed float, ignore spacing*))) FAILURE
              } else FAILURE
            }
          */
        }
      }

      return $res;

    }

    /*

    function standardCheck(params) {
      for ($param in $params) {
        regEx.test($param, "FIT FORMAT: '(up to 20 alphanumerics or - or _)' ")
      }
    }

    */

  }
?>
