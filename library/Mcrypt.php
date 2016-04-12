<?php
class Mcrypt {

    private $td;
    private $iv;
    private $ks;
    private $salt;
    private $encStr;
    private $decStr;


    /**
     *  The constructor initializes the cryptography library
     * @param $salt string The encryption key
     * @return void
     */
    function __construct($salt = null) {
      if(is_null($salt)) {
        $salt = Config::app('encryptSecretKey');
      }
      $this->td = mcrypt_module_open('rijndael-256', '', 'ofb', ''); // algorithm
      $this->ks = mcrypt_enc_get_key_size($this->td); // key size needed for the algorithm
      $this->salt = substr(md5($salt), 0, $this->ks);
    }

    /**
     * Generates a hex string of $src
     * @param $src string String to be encrypted
     * @return String
     */
    public function encrypt($src) {
        srand(( double) microtime() * 1000000); //for sake of MCRYPT_RAND
        $this->iv = mcrypt_create_iv($this->ks, MCRYPT_RAND);
        mcrypt_generic_init($this->td, $this->salt, $this->iv);
        $tmpStr = mcrypt_generic($this->td, $src);
        mcrypt_generic_deinit($this->td);
        mcrypt_module_close($this->td);

        //convert the encrypted binary string to hex
        //$this->iv is needed to decrypt the string later. It has a fixed length and can easily
        //be seperated out from the encrypted String
        $this->encStr = bin2hex($this->iv.$tmpStr);
        return $this->encStr;
    }

    /**
     * Decrypts a hex string
     * @param $src string String to be decrypted
     * @return String
     */
    public function decrypt($src) {

        //convert the hex string to binary
        $corrected = preg_replace("[^0-9a-fA-F]", "", $src);
        $binenc = pack("H".strlen($corrected), $corrected);

        //retrieve the iv from the encrypted string
        $this->iv = substr($binenc, 0, $this->ks);

        //retrieve the encrypted string alone(minus iv)
        $binstr = substr($binenc, $this->ks);

        /* Initialize encryption module for decryption */
        mcrypt_generic_init($this->td, $this->salt, $this->iv);
        /* Decrypt encrypted string */
        $decrypted = mdecrypt_generic($this->td, $binstr);

        /* Terminate decryption handle and close module */
        mcrypt_generic_deinit($this->td);
        mcrypt_module_close($this->td);
        $this->decStr = trim($decrypted);
        return $this->decStr;
    }
}
