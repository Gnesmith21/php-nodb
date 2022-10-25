<?php
/**
 * Copyright 2022 Gavin Nesmith

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
 */

/**
 * Low level key value db meant to store configuration data for applications
 * the db is json based and will have verification and encryption
 * @author Gavin R. Nesmith
 * @version 1.2
 * @copyright 2022
 */


class phpnodb
{
    private $data = null;

    private $password = ""; # not implemented.

    private $readOnly = false;

    private $filehash = null;

    private $filename = "";

    private $changed = false;

    private $checksum = "";

    /**
     * @throws dbexception
     */
    function __construct(string $filename, bool $isencrypted = null)
    {
        $temp = explode('.', $filename);
        $extension = end($temp);

        #check the extension
        if ($extension != "nodb") {
            if (!$extension) {
                $filename = ($filename . ".nodb");
            } else {
                throw new dbexception("File type not supported");
            }
        }

        //check if we have the file
        if (!file_exists($filename)) { #Check for the file
            $this->genfile($filename); #Create the file
        }

        $rawfiledata = json_decode(file_get_contents($filename), true); #save the raw data
        $this->filehash = $this->genChecksum($filename); #Store the checksum for later
        $this->checksum = $rawfiledata["CheckSum"]; #stored checksum from file.
        $this->filename = $filename; #Save filename

        #need to check for encryption
        if ($isencrypted) {
            $unencrypted = self::decryptData($rawfiledata['data']);
            $this->data = $unencrypted;
        }

        #if all else fails get the data

        if (empty($this->data)) {
            $this->data = $rawfiledata['data'];
        }

    }

    /**
     * Generates a file if it is not found during class construction
     * @param $filename
     * @param null $data
     * @param null $checksum
     * @return false|int
     */
    private function genfile($filename, $data = null, $checksum = null)
    {

        // input data  through array
        $array = array(
            "data" => $data,
            "CheckSum" => "{$checksum}"
        );

        // encode array to json
        $json = json_encode($array);
        //generate json file
        return file_put_contents("$filename", $json, LOCK_EX);
    }

    /**
     * This function generates a checksum that will be verified
     * each time the file is modified to ensure the origin of the file modification
     * @param string $filename
     * @return false|string
     * @since 1.0.0
     */
    private function genChecksum(string $filename)
    {
        return md5_file($filename); #computes the MD5
    }

    /**
     * function net yet implemented
     * @param string $encryptedString
     * @return string
     */
    private static function decryptData(string $encryptedString)
    {
        return $encryptedString; //TODO:: fix this to make it decrypt.
    }

    /**
     * function not yet implemented.
     * @param string $rawfiledata
     * @return string
     */
    private static function encryptDate(string $rawfiledata)
    {
        return $rawfiledata; // TODO:: fix this so we can encrypt
    }

    /**
     * Enables db in read mode. this can not be writen to
     * @return $this
     */
    public function enableReads()
    {
        if ($this->readOnly == false) {
            $this->readOnly = true;
        }


        return $this;
    }

    /**
     * Enabled the DB to be written to a file
     * @return $this
     */
    public function enableWrites()
    {
        if ($this->readOnly) {
            $this->readOnly = false;
        }


        return $this;
    }

    /**
     * sets value in DB. throws exception if db in readonly mode
     * @throws dbexception
     */
    public function set($key, $value)
    {
        if ($this->readOnly) {
            throw new dbexception("The db is currently in readonly mode.", 3);
        }

        if (!$this->changed) {
            $this->changed = true; # if we made it this far then set the change flag.
        }

        if (is_array($value)) {
            $value = json_encode($value); # convert arrays to strings.
        } else {
            $value = (string)$value;
        }

        $this->data[$key] = $value; //todo:: make this work with multidimensional data.

        return true;

    }

    public function get($key)
    {
        return $this->data[$key];
    }

    public function delete($key)
    {
        if (!$this->readOnly) {
            if (isset($this->data[$key])) {
                unset($this->data[$key]);
                return true;
            }
            return false;
        } else {
            throw new dbexception("File is read only an may not be changed.");
        }
    }

    /**
     * function not implemented.
     * @param string $filename
     * @return void
     */
    public function verifyChecksum(string $filename)
    {
    }

    /**
     * commits the changes to the dbfile
     * @return false
     * @throws dbexception
     */
    public function commit()
    {
        if (!$this->readOnly) {
            $commit = $this->genfile($this->filename, $this->data, $this->checksum);

            if (!$commit) {
                throw new dbexception("Can not save the changes for this file.");
            } else {
                $this->changed = false; #revert to false after save;
                return true;
            }
        }
        return false;
    }

    /**
     * function validates changes.
     * if not false. if yes then true
     * @return boolean
     */
    private function checkChanges()
    {
        return $this->changed;
    }

    /**
     * destructor for the class. used for change validation.
     * If uer has unsaved settings and running in protect mode throw error
     * @throws dbexception
     */
    function __destruct()
    {
        if ($this->checkChanges()) {
            throw new dbexception(" Error: Unsaved changes have been discarded on no SQL db. Remember to commit.");
        }
    }
}
