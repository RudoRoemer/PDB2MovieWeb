<?php
    class FileChecker {

        protected $mimeTypes;
        protected $file;
        protected $ext;
        protected $hashedName;
        protected $tmpLocation;
        protected $checkRes;

        protected function baseCheck() {

            global $configs;

            try {

                if ($this->hashedName == null) {
                    $this->hashedName = "YourFile";
                }

                if (!isset($_FILES[$this->file]['error']) || is_array($_FILES[$this->file]['error'])) {
            	    throw new RuntimeException('Invalid parameters.' . $this->file);
            	}

                //generic errors.
                switch ($_FILES[$this->file]['error']) {
                    case UPLOAD_ERR_OK:
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        throw new RuntimeException('No file sent.');
                    case UPLOAD_ERR_INI_SIZE:
                        throw new RuntimeException('Upload INI size error.');
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new RuntimeException('Exceeded filesize limit.');
                    default:
                        throw new RuntimeException('Unknown errors.');
                }
                if ($_FILES[$this->file]['size'] > 10485760) { //10mB
                    throw new RuntimeException('Exceeded filesize limit.');
                }

                //make sure correct filetype.
                $mtype = mime_content_type($_FILES[$this->file]['tmp_name']);
                $flag = false;
                foreach ($this->mimeTypes as &$mimeType) {
                    if (strcmp($mtype, $mimeType) === 0) {
                         $flag = true;
                    }
                }

                if ($flag === false) {
                    throw new RuntimeException('Incorrect filetype ' . $mtype . " " . $mimeType . " String comparison result: " . strcmp($mtype, $mimeType));
                }

                unset($mimeType);

                $this->tmpLocation = sprintf($configs["localDirectory"] . '/php/pdb_tmp/%s.%s', $this->hashedName, $this->ext);
		$this->checkRes = "Success"; 

            } catch (RuntimeException $e) {
		$this->checkRes = $e->getMessage();
            }
        }

        public function setMimeTypes($list) {
            $this->mimeTypes = $list;
        }
        public function setFile($fileToSet) {
            $this->file = $fileToSet;
        }
        public function setExt($extToSet) {
            $this->ext = $extToSet;
        }
        public function setUniqueName($hashToSet) {
            $this->hashedName = $hashToSet;
        }

        public function getTmpLocation(){
            return $this->tmpLocation;
        }

        public function didItPass() {
            return $this->checkRes;
        }
    }
?>
