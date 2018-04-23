<?php

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

                $file=file_get_contents( $this->getTmpLocation() );
                $remove = "\n";
                $split = explode($remove, $file);
                $lCount = 1;
                foreach ($split as $str) {

                    $san = filter_var($str, FILTER_SANITIZE_SPECIAL_CHARS);
                    if ($san !== $str && substr($str,0,6) !== "REMARK") {
                        if (strpos($str, "'") !== false && (strpos($str, "HETNAM") === false AND strpos($str, "HETATM") === false)) {
                            $hasPos = strpos($str, "'");
                            $isHet = strpos($str, "HETNAM");
                            $this->checkRes = sprintf(". At least one unexpected character found at line %s of .pdb file. hasApos: %s isHet: %s str: %s", $lCount,  $hasPos, $isHet, $str);
                            break;
                        }
                    }

                    $lCount++;/**/

                }
            }
        }
    }
?>
