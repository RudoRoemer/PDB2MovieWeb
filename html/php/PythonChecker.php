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
        /* TODO: white list of py commands for each line. This is difficult to achieve with no knowledge of the innter workings and security of the freemol extension for pymol.
        So far have found no one who knows the workings of that, nor anyone who is willing to try to penetrate security via a .py file. Talks with CSC may be needed for that
        one, as well as confirmation from the person attacking the system that they will do nothing harmful. This - for now until further information is give - seems out of my
        job scope.

        For now, I will add in a feature taht will make sure that the first line starts with the initialisation, and that the next section is a specific function that only
        allow for the camera position to be changed.

        Another suggestion is a selection of server-side python scripts for the user to choose from.*/
      }

      return $res;

    }
  }
?>
