<?php
class Mcrypt
{
    private $iv;
    private $cipher;
    private $key;
    private $encStr;
    private $decStr;
    private $options = 0;
    private $tag = null;

    /**
     *  The constructor initializes the cryptography library
     * @param $salt string The encryption key
     * @return void
     */
    public function __construct()
    {
        $this->cipher = Config::app('cipher');
        assert(in_array($this->cipher, openssl_get_cipher_methods()), $this->cipher . ' NotFound.');
        $this->key = Config::app('encryptSecretKey');
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $this->iv = openssl_random_pseudo_bytes($ivlen);
        $this->iv = bin2hex($this->iv);
        // exit(var_export($this->iv));
    }

    /**
     * Generates a hex string of $src
     * @param $src string String to be encrypted
     * @return String
     */
    public function encrypt($src)
    {
        $this->encStr = openssl_encrypt($src, $this->cipher, $this->key, $this->options, $this->iv);
        return trim($this->encStr . $this->iv);
    }

    /**
     * Decrypts a hex string
     * @param $src string String to be decrypted
     * @return String
     */
    public function decrypt($src)
    {
        $this->iv = substr($src, -32);
        $src = substr($src, 0, strlen($src) - 32);
        $this->decStr = openssl_decrypt($src, $this->cipher, $this->key, $this->options, $this->iv);
        return trim($this->decStr);
    }
}
