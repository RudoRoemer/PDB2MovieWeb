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

      global $configs;
      $this->baseCheck();

      if ($this->checkRes === "Success") {

        $file=file_get_contents($_FILES[$this->file]['tmp_name']);
        $comp=file_get_contents("../../python-whitelist.txt");
        $bloc=file_get_contents("../../python-blacklist.txt");
        $remove = "\n";
        $split = explode($remove, $file);
        $csplt = explode($remove, $comp);
        $bsplt = explode($remove, $bloc);
        $acc = 0;
        $opnBrac = false;
        $blackListFlag = false;

        foreach ($split as $str) {

          $acc++;
          $str = preg_replace("/[^a-zA-Z0-9(),;:._# ]+/", "", $str);
          $wlNoBracs;
          $tmp;

          $hasOpnBrac = strpos($str, "(");
          $hasClsBrac = strpos($str, ")");

          //$this->checkRes .= "\n-------\n";
          //$this->checkRes .= "hasOpnBrac=" . $hasOpnBrac . "\n";
          //$this->checkRes .= "hasClsBrac=" . $hasClsBrac . "\n";
          //$this->checkRes .= "opnBrac=" . $opnBrac . "\n";
          //$this->checkRes .= "str=" . $str . "\n";

          $match = false;

          if (substr($str, 0, 1) === "#" || $str == "") {
            continue 1;
          }

          if ($configs["useBlackList"] === "1") {
            foreach($bsplt as $bl) {

              $bl = preg_replace("/[^a-zA-Z0-9(),;:._# ]+/", "", $bl);


              if (strpos($str, $bl) !== false ) {

                $blackListFlag = true;
                break 2;

              };
            }
          }

          if ($configs["useWhiteList"] === "1") {

            foreach($csplt as $wl) {

              $tmp = strpos($wl, "()");
              $wl = preg_replace("/[^a-zA-Z0-9,;:._ ]+/", "", $wl);
              if (strpos($str, $wl) !== false && $wl !== null) { $match = true; $wlwithBracs = ( strpos($wl, "()") !== false ? true : false ); break 1; }

            }

            if ($opnBrac) {

              if ($hasOpnBrac !== false) {

                $this->checkRes = "Illegal syntax at line " . $acc . ", reads:a " . $str;
                return $this->checkRes;
                break;

              }

              if ($hasClsBrac !== false) {

                $opnBrac = false;

              }

              if ($match !== false) {

                $this->checkRes = "Illegal syntax at line " . $acc . ", reads:b " . $str;
                break;
              }

            } else if ($match) {

              if ($hasOpnBrac === false && $wlwithBracs ) {

                var_dump($str);
                var_dump();
                $this->checkRes = "Illegal syntax at line " . $acc . ", reads:c " . $str;
                break;

              } else {

                if ($hasClsBrac === false && $hasOpnBrac ) {

                  $opnBrac = true;
                  continue;

                }
              }
            } else {
              $this->checkRes = "Illegal syntax at line " . $acc . ", reads:d " . $str;
              break;

            }

          }

        }
      }

      if ($blackListFlag) {$this->checkRes = "command at line " . --$acc . " is black listed, reads:\n\n" . $split[$acc]; }
      return $this->checkRes;

    }

    /*
    I'm sorry you just had to read that. It wasn't pretty to look at, it isn't easy to understand, it wasn't fun to write; and if you're furthering this development section, I truely am so sorry.
    Given this a second go around. I would define three different types - declarations, commands, lists maths. declarations would be quite easy. Maths would have a runtime whitelist of vars declared.
    So far this code works for every example of code I have been given.
    */

  }
?>
