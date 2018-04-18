<?php

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
                /* TODO: white list of py commands for each line */
            }

            return $res;

        }
    }
?>
